<?php if ( ! defined('ROOT_PATH')) exit('No direct script access allowed');
/**
 * Model pada tipe-konten Video
 *
 * @package  ContentType\Video\Models
 * @author   Iyan Kushardiansah <iyank4@gmail.com>
 */
class VideoModel extends ContentTypeModel
{
    public $table_name = 'ct_video';
    public $fields = array(
                        'title' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 120
                                ),
                        'author' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 65
                                ),
                        'pub_date' => array(
                                    'type' => 'DATETIME'
                                ),
                        'description' => array(
                                    'type' => 'TEXT'
                                ),
                        'tags' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'image' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'video_file' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'youtube_id' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'counter' => array(
                                    'type' => 'BIGINT',
                                    'unsigned' => TRUE,
                                    'default' => 0
                                )
                     );

    public $keys = array('pub_date');
}
