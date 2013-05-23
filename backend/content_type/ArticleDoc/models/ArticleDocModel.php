<?php if ( ! defined('ROOT_PATH')) exit('No direct script access allowed');
/**
 * Model pada tipe-konten Article
 *
 * @package  ContentType\Article\Models
 * @author   Iyan Kushardiansah <iyank4@gmail.com>
 */
class ArticleDocModel extends ContentTypeModel
{
    public $table_name = 'ct_article_doc';
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
                        'file1' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'file2' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'file3' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'file4' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'file5' => array(
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

    public $keys = array('slug', 'pub_date', 'tags');
}
