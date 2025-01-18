<?php

namespace Sholokhov\Exchange\ORM;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Fields;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\SystemException;

abstract class AbstractXmlDynamic extends DataManager
{
    public static function getMap(): array
    {
        $attributeField = (new Fields\TextField('ATTRIBUTES'))->configureNullable();
        $attributeField->setSerialized();

        return [
            'ID' => (new Fields\IntegerField('ID'))
                ->configureSize(8)
                ->configureAutocomplete()
                ->configurePrimary(),
            'SESS_ID' => (new Fields\StringField('SESS_ID'))->configureSize(8),
            'PARENT_ID' => (new Fields\IntegerField('PARENT_ID'))->configureSize(8)->configureNullable(),
            'LEFT_MARGIN' => (new Fields\IntegerField('LEFT_MARGIN'))->configureNullable(),
            'RIGHT_MARGIN' => (new Fields\IntegerField('RIGHT_MARGIN'))->configureNullable(),
            'DEPTH_LEVEL' => (new Fields\IntegerField('DEPTH_LEVEL'))->configureNullable(),
            'NAME' => (new Fields\StringField('NAME'))->configureSize(255)->configureNullable(),
            'VALUE' => (new Fields\TextField('VALUE'))->configureLong()->configureNullable(),
            'ATTRIBUTES' => $attributeField,
        ];
    }

    public static function getElement(int $leftMargin, int $rightMargin): array
    {
        $arElement = array();

        $db_com = parent::getList(
            array(
                "filter" => array(
                    "><LEFT_MARGIN" => array($leftMargin, $rightMargin),
                ),
                "select" => array("*")
            )
        );
        $arDepth = array();

        $arParams = array("replace_space"=>"_","replace_other"=>"_");

        while ($ar_com = $db_com->fetch()) {

            foreach($arDepth as $keyD => $valueD){
                if(intval($keyD)>=intval($ar_com["DEPTH_LEVEL"])){
                    unset($arDepth[$keyD]);
                }
            }

            $codePropPrev = \CUtil::translit(implode("_",$arDepth),"ru",$arParams);
            unset($arElement[$codePropPrev]);
            $arDepth[$ar_com["DEPTH_LEVEL"]] = $ar_com["NAME"];
            $codeProp = \CUtil::translit(implode("_",$arDepth),"ru",$arParams);

            $ar_com["NAME"] = $codeProp;

            if (isset($arElement[$ar_com["NAME"]])) {
                if (!is_array($arElement[$ar_com["NAME"]])) {
                    $temp = $arElement[$ar_com["NAME"]];
                    $arElement[$ar_com["NAME"]] = array();
                    $arElement[$ar_com["NAME"]][] = $temp;
                }
                $arElement[$ar_com["NAME"]][] = $ar_com["VALUE"];
            } else {
                $arElement[$ar_com["NAME"]] = $ar_com["VALUE"];
            }

            if(!empty($ar_com["ATTRIBUTES"])){
                foreach ($ar_com["ATTRIBUTES"] as $keyAttribute => $attribute) {
                    $attributeCode = $ar_com["NAME"]  . "_attribute_" . $keyAttribute;
                    if (isset($arElement[$attributeCode])) {
                        if (!is_array($arElement[$attributeCode])) {
                            $temp = $arElement[$attributeCode];
                            $arElement[$attributeCode] = array();
                            $arElement[$attributeCode][] = $temp;
                        }
                        $arElement[$attributeCode][] = $attribute;
                    }else{
                        $arElement[$attributeCode] = $attribute;
                    }
                }
            }
        }

        return $arElement;
    }


    /**
     * Получение всех дочерних элементов по наименованию тега
     *
     * @param string $name
     * @param int $depth
     * @return array
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getElementsByName(string $name, int $depth = 1): array
    {
        $parent = self::getRow(['filter' => ['=NAME' => $name, '=DEPTH_LEVEL' => $depth]]);

        if (!$parent) {
            return [];
        }

        $filter = ['=PARENT_ID' => $parent['ID']];
        $select = ['LEFT_MARGIN', 'RIGHT_MARGIN'];

        return self::getList(compact('filter', 'select'))->fetchAll();
    }
}