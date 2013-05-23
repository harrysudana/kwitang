<?php if ( ! defined('ROOT_PATH')) exit('No direct script access allowed');
/**
 * Model pada tipe-konten Agenda
 *
 * @package  ContentType\Agenda\Models
 * @author   Iyan Kushardiansah <iyank4@gmail.com>
 */
class AgendaModel extends ContentTypeModel
{
    public $table_name = 'ct_agenda';
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
                        'counter' => array(
                                    'type' => 'BIGINT',
                                    'unsigned' => TRUE,
                                    'default' => 0
                                ),
                        'date_start' => array(
                                    'type' => 'DATETIME',
                                ),
                        'date_end' => array(
                                    'type' => 'DATETIME',
                                    'null' => true
                                ),
                        'time' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => true,
                                    'default' => null
                                ),
                        'venue' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255
                                ),
                        'counter' => array(
                                    'type' => 'BIGINT',
                                    'unsigned' => TRUE,
                                    'default' => 0
                                )
                     );

    public $keys = array('slug', 'pub_date', 'tags', 'date_start');
}
