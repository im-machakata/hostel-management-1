<?php
require_once 'Request.php';
// @session_start();
/**
 * Returns a session value if provided or sets value to session key
 *
 * @param string $key
 * @param mixed $value
 * @return void|mixed
 */
function session($key, $value = '')
{
    if ($value === '') {
        return $_SESSION[$key] ?? null;
    }
    $_SESSION[$key] = $value;
}

/**
 * Use component files in your ui.
 *
 * @param string $__name__ component name
 * @param array $__data__ component data
 * @return void
 */
function render_component($__name__, $__data__ = [])
{
    extract($__data__);
    $__FILE__ =    __DIR__ . '/../Views/' . $__name__ . '.php';
    if (file_exists($__FILE__)) {
        require $__FILE__;
    }
}


function url_active($url)
{
    if (\App\System\Request::isUrl($url)) {
        return ' active';
    }
    return '';
}

function user()
{
    return new class
    {
        public $id = null;
        private $row = [];

        public function __construct()
        {
            $db = new \App\System\Database();

            if ($this->getId()) {
                $db->prepare("SELECT * FROM users WHERE id = :id LIMIT 0,1", [
                    'id' => $this->getId()
                ])->execute();
                $this->row = $db->getRow();
            }
        }

        public function getId()
        {
            return session('user');
        }

        public function __get($name)
        {
            return $this->row[$name] ?? null;
        }
    };
}
