<?php if ( ! defined('ROOT_PATH')) exit('No direct script access allowed');
/**
 * Model pada tipe-konten Page
 *
 * @package  ContentType\Page\Models
 * @author   Iyan Kushardiansah <iyank4@gmail.com>
 */
class PageModel extends ContentTypeModel
{
    public $table_name = 'ct_page';
    public $fields = array(
                        'title' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 160
                                ),
                        'slug' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 160
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
                        'body' => array(
                                    'type' => 'LONGTEXT'
                                ),
                        'tags' => array(
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

    public $keys = array('slug', 'pub_date', 'tags', 'counter');
}
