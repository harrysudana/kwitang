<?php if ( ! defined('ROOT_PATH')) exit('No direct script access allowed');
/**
 * Model pada tipe-konten Text
 *
 * @package  ContentType\Text\Models
 * @author   Iyan Kushardiansah <iyank4@gmail.com>
 */
class TextModel extends ContentTypeModel
{
    public $table_name = 'ct_text';
    public $fields = array(
                        'title' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 160
                                ),
                        'body' => array(
                                    'type' => 'LONGTEXT'
                                )
                     );

    //public $keys = array('slug', 'pub_date', 'tags', 'guid');
}
