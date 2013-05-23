<?php if ( ! defined('ROOT_PATH')) exit('No direct script access allowed');
/**
 * Model pada tipe-konten Photo
 *
 * @package  ContentType\Photo\Models
 * @author   Iyan Kushardiansah <iyank4@gmail.com>
 */
class PhotoModel extends ContentTypeModel
{
    public $table_name = 'ct_photo';
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
                        'foto1' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'description1' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'foto1' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'description1' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'foto2' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'description2' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'foto3' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'description3' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'foto4' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'description4' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'foto5' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'description5' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'foto6' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'description6' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'foto7' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'description7' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'foto8' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'description8' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'foto9' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'description9' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'foto10' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'description10' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'foto11' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'description11' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'foto12' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'description12' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'foto13' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'description13' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'foto14' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'description14' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'foto15' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'description15' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'foto16' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'description16' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'foto17' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'description17' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'foto18' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'description18' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'foto19' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'description19' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'foto20' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'description20' => array(
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
