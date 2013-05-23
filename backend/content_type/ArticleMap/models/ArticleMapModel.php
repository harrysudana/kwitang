<?php if ( ! defined('ROOT_PATH')) exit('No direct script access allowed');
/**
 * Model pada tipe-konten ArticleMap
 *
 * @package  ContentType\ArticleMap\Models
 * @author   Iyan Kushardiansah <iyank4@gmail.com>
 */
class ArticleMapModel extends ContentTypeModel
{
    public $table_name = 'ct_articlemap';
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
                        'thumbnail' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'foto' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
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
                        'guid' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'permalink' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'counter' => array(
                                    'type' => 'BIGINT',
                                    'unsigned' => TRUE,
                                    'default' => 0
                                ),
                        'lat' => array(
                                    'type' => 'DOUBLE',
                                    'unsigned' => FALSE,
                                    'default' => -7.5
                                ),
                        'lng' => array(
                                    'type' => 'DOUBLE',
                                    'unsigned' => FALSE,
                                    'default' => 112
                                ),
                     );

    public $keys = array('slug', 'pub_date', 'tags', 'guid');
}
