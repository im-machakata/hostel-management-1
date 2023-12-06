<?php

use App\System\Database;
use App\System\Response;
use App\System\Request;

// include autoloader
include __DIR__ . "/../src/autoload.php";
$rooms = $errors = [];

// check if user is logged / authenticated
if (!user()->getId()) {
    return Response::redirect('/login.php');
}

// initiate db
$db = new Database();

#region save/update table before we fetch new results
if (Request::isPost()) {
    $name = Request::post('roomName');
    $price = Request::post('roomPrice');
    $details = Request::post('roomDescription');
    $booked = Request::post('roomBooked');
    $file = $_FILES['roomImage'] ?? null;
    $newFileName = false;

    if(Request::getVar('action') == 'delete'){
        $db->prepare("DELETE FROM rooms WHERE id = :id", [
            'id' => Request::post('id')
        ])->execute();
        return Response::redirect('/manage-rooms.php');
    }

    if ($file && $file['error'] != UPLOAD_ERR_NO_FILE) {

        // check if file is a valid image
        if (!getimagesize($file['tmp_name'])) {
            $errors[] = 'Please upload a valid image file.';
        }

        if (!$errors) {
            // move file to assets
            $targetDir = __DIR__ . '/assets/uploads/';
            $newFileName = uniqid() . '-' . $file['name'];
            $targetFile = $targetDir . $newFileName;

            if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
                $errors[] = 'There was an error uploading your image.';
            }
        }
    }

    if ($name && $price && $details && !$errors) {
        $saved = false;
        $data = [
            'name' => $name,
            'cost' => $price,
            'description' => $details,
            'is_booked' => $booked == '1' ? '1' : '0'
        ];

        // check for duplicates if new entry
        if (Request::post('action') != 'edit') {
            $db->prepare('SELECT id FROM rooms WHERE name = :name AND cost = :cost AND description = :description AND is_booked = :is_booked LIMIT 0,1', $data)->execute();

            if ($db->getRow()) {
                $errors[] = 'Room already exists with the same details.';
            }
        }

        if (!$errors) {
            $query = 'INSERT INTO rooms (name, cost, description, is_booked%s) VALUES (:name, :cost, :description, :is_booked%s)';
            if (Request::post('action') === 'edit') {
                $data['id'] = Request::post('id');
                $query = 'UPDATE rooms SET name = :name, cost = :cost, description = :description, is_booked = :is_booked%s WHERE id = :id';
            }
            if ($newFileName) {
                $data['image_url'] = '/assets/uploads/' . $newFileName;
                
                // add image url to query
                if (Request::post('action') === 'edit') {
                    $query = sprintf($query, ' AND image_url = :image_url');
                } else {
                    $query = sprintf($query, ', image_url', ', :image_url');
                }
            } else {
                $query = sprintf($query, '', '');
            }
            $saved = $db->prepare($query, $data)->execute();
            if (!$saved) $errors[] = 'Failed to save new room.';
        }
    }

    // we will only get here if some fields are missing
    if (!$errors && !$saved) {
        $errors[] = 'Some fields are missing or empty.';
    }
}
#endregion

#region get available rooms
$db->prepare('SELECT * FROM rooms')->execute();
$rooms = $db->getRows();
#endregion

#region show error if rooms array empty
if (!$rooms && !$errors) :
    $errors[] = 'There\'s not any rooms at the moment.';
endif;
#endregion

render_component('head', ['title' => 'Manage Rooms']);
?>

<body>
    <?php render_component('menu'); ?>
    <main>
        <div class="container-fluid">
            <?php render_component('header', ['page' => 'Manage Rooms']); ?>
            <p class="d-flex justify-content-lg-center mb-5"><a href="#newRoom" data-bs-toggle="modal" data-bs-target="#newRoom" class="btn btn-success border-0 col-lg-2" data-id="" data-action="new">Add New Room</a></p>
            <section class="px-0">
                <!-- Edit / New Room Modal -->
                <div class="modal fade" id="newRoom" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="newRoomLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="newRoomLabel">New Room</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="/manage-rooms.php" method="post" enctype="multipart/form-data">
                                    <input id="roomID" type="hidden" name="id" value="null">
                                    <input id="formAction" type="hidden" name="action" value="new-room">
                                    <div class="mb-3">
                                        <label for="roomName" class="form-label">Room Name</label>
                                        <input type="text" class="form-control" placeholder="Room Name" id="roomName" name="roomName" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="roomPrice" class="form-label">Room Price</label>
                                        <input type="number" class="form-control" placeholder="Room Price" id="roomPrice" name="roomPrice" required>
                                    </div>
                                    <div class="mb-3 input-group">
                                        <input type="file" class="form-control" placeholder="Room Image" id="roomImage" name="roomImage">
                                    </div>
                                    <div class="mb-3 form-check">
                                        <label class="form-check-label" for="roomBooked">
                                            The room is already booked
                                        </label>
                                        <input class="form-check-input" type="checkbox" value="1" id="roomBooked" name="roomBooked">
                                    </div>
                                    <div class="mb-3">
                                        <label for="roomDescription" class="form-label">Room Description</label>
                                        <textarea type="text" class="form-control" placeholder="Room Description" id="roomDescription" name="roomDescription" style="height: 100px;" required></textarea>
                                    </div>
                                    <hr class="mt-2">
                                    <div class="my-2 px-1 text-end">
                                        <button type="button" class="btn btn-outline-dark w-25" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-success border-0 w-25">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Success Layouts -->
                <?php if (Request::isPost() && !$errors) : ?>
                    <div class="alert alert-success">
                        <?php
                        if (Request::getVar('action') == 'edit') {
                            echo 'Room has been updated.';
                        } elseif (Request::getVar('action') == 'delete') {
                            echo 'Selected room has been deleted.';
                        } else {
                            echo 'New room has been captured.';
                        } ?>
                    </div>
                <?php endif; ?>

                <div class="row mt-2 justify-content-center">
                    <!-- Errors Layouts -->
                    <?php render_component('errors', ['errors' => $errors]); ?>

                    <?php foreach ($rooms as $room) : ?>
                        <div class="col-lg-3 mb-3">
                            <?php render_component('room', $room) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
    </main>
    <?php render_component('footer'); ?>
</body>

</html>