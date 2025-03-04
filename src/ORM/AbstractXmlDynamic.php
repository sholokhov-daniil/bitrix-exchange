<?php

namespace Sholokhov\Exchange\ORM;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Fields;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\SystemException;
use CUtil;

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
        $result = [];
        $depths = [];
        $translitOptions = array("replace_space"=>"_","replace_other"=>"_");

        $iterator = self::query()
            ->addFilter('><LEFT_MARGIN', [$leftMargin, $rightMargin])
            ->addSelect('*')
            ->exec();

        while ($item = $iterator->fetch()) {
            foreach($depths as $keyD => $valueD){
                if(intval($keyD)>=intval($item["DEPTH_LEVEL"])){
                    unset($depths[$keyD]);
                }
            }

            $codePropPrev = CUtil::translit(implode("_",$depths),"ru",$translitOptions);
            unset($result[$codePropPrev]);

            $depths[$item["DEPTH_LEVEL"]] = $item["NAME"];
            $item["NAME"] = CUtil::translit(implode("_",$depths),"ru",$translitOptions);

            if (isset($result[$item["NAME"]])) {
                if (!is_array($result[$item["NAME"]])) {
                    $result[$item["NAME"]] = [$result[$item["NAME"]]];
                }
                $result[$item["NAME"]][] = $item["VALUE"];
            } else {
                $result[$item["NAME"]] = $item["VALUE"];
            }

            if(!empty($item["ATTRIBUTES"])){
                foreach ($item["ATTRIBUTES"] as $keyAttribute => $attribute) {
                    $attributeCode = $item["NAME"]  . "_attribute_" . $keyAttribute;
                    if (isset($result[$attributeCode])) {
                        if (!is_array($result[$attributeCode])) {
                            $result[$attributeCode] = [$result[$attributeCode]];
                        }
                        $result[$attributeCode][] = $attribute;
                    } else {
                        $result[$attributeCode] = $attribute;
                    }
                }
            }
        }

        return $result;
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