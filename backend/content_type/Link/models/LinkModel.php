<?php if ( ! defined('ROOT_PATH')) exit('No direct script access allowed');
/**
 * Model pada tipe-konten Link
 *
 * @package  ContentType\Link\Models
 * @author   Iyan Kushardiansah <iyank4@gmail.com>
 */
class LinkModel extends ContentTypeModel
{
    public $table_name = 'ct_link';
    public $fields = array(
                        'title' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 120
                                ),
                        'url' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255
                                ),
                        'image' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE,
                                    'default' => NULL
                                )
                     );

    //public $keys = array('slug', 'pub_date', 'tags', 'guid');
}
