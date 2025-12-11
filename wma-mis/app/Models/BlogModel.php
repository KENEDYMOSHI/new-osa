<?php namespace App\Models;

use CodeIgniter\Model;
class BlogModel extends Model{
    protected $table         = 'posts';
    protected $primaryKey    = 'post_id';
    protected $allowedFields = ['post_title','post_content'];

    protected $useTimestamps = true;
    protected $createdField  = 'post_created_at';
    protected $updatedField  = 'post_updated_at';
    protected $deletedField  = 'post_deleted_at';
}

?>