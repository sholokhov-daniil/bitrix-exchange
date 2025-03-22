# Пользовательский обмен данных

## Автор
Шолохов Даниил - <sholokhovdaniil@yandex.ru> - <https://t.me/sholokhov22>

## Минимальные требования

* Версия php 8.2

* Реакция bitrix: старт

## Краткое описание
Модуль обмена позволяет производить импорт\экспорт в любые сущности. Главная цель - регламентированный и централизованный обмен данными.

## Краткое описание бизнес логики

* Разработчик выбирает сущность в которую будет происходить импорт
* Разработчик указывает источник данных(итератор)
* Разработчик формирует карту импорта (откуда-куда), которая регламентирует обработку значений источника
* Запускается обмен
* Обмен итерационно производит получение значений из источника
* Нормализует данные источника на основе карты
* Запускает "подимпорт" свойства, если это необходимо (сохранение файла, создание связанного элемента сущности и т.д.)
* Определяется существование элемента сущности, если оно существует, то производит обновление, в противном случае создает
* Деактивация старых значений (опционально)

## Описание структуры
Все импорты хранятся в директории: **Target**

Все стандартные источники данных хранятся в директории: **Source**

Стандартные нормализаторы данных хранятся в директории: **Normalizers**

Стандартные поля описания карты импорта хранятся в директории: **Fields**

## Инициализация импорта

````php
use Sholokhov\Exchange\Target\IBlock\Element;

$exchange = new Element($options);
$exchange->setMap($map);
$result = $exchange->execute($iterator);
````

Каждый обмен предусматривает пользовательскую конфигурацию, который позволит включить деактивацию старых импортируемых значений.
При необходимости регламентированная конфигурация может расширяться.

````php
$options = [
    'deactivate' => true
];
$exchange = new Element($options);
````

## Карта обмена

### Описание структуры элемента карты обмена
Карта импорта представляет собой массив объектов, которые обязаны реализовывать интерфейс

````
\Sholokhov\Exchange\Fields\FieldInterface
````

Стандартный набор методов описания свойства сущности
* **setKeyField(boolean)** - Поле выступает в качестве идентификационного поля(связывает элементы сущности и элементы источника данных) **<font color="#ff0000">(обязательное)</font>**
* **setPath(string)** - Путь до значения, которое вернул источник. Если путь состоит из вложеностей массива, то каждый уровень разделяется символом "." **<font color="#ff0000">(обязательное)</font>**
* **setCode(string)** - В какой ключ будет записываться значение из структуры данных источника **<font color="#ff0000">(обязательное)</font>**
* **setMultiple(boolean)** - Свойство, в которое мы производим импорт является множественным
* **setTarget(\Sholokhov\Exchange\ExchangeInterface)** - Вложенный обмен данных - результат обмена будет выступать значением свойства
* **setNormalizers(callable)** - Пользовательский нормализатор значения - хорошо подходит, если нам нужно немного обработать значение источника данных (изменить формат даты и т.п.)

### Инициализация карты обмена

````php
use Sholokhov\Exchange\Fields;
use Sholokhov\Exchange\Target\IBlock\Element;

$exchange = new Element($options);
$exchange->setMap($map);

$map = [
    (new Fields\Field)
        ->setPath('path')
        ->setCode('NEW_KEY')
        ->setMultiple(true)
        ->setTarget($exchange)
        ->setNormalizers(fn($value) => $value + 2)
        ->setKeyField(true),
    (new Fields\IBlock\IBlockElementField)
        ->setPath('...')
        ->setCode('...')
        ->setProperty(true), // Поле является свойством ИБ
];
````

## Источник данных импорта
Каждый источник обязан реализовать интерфейс [Iterator](https://www.php.net/manual/ru/class.iterator.php)

### Пример инициализации итератора

````php
use Sholokhov\Exchange\Target\IBlock\Element;

$items = [
    ['NAME' => 'test1'],
    ['NAME' => 'test2']
];

$iterator = new ArrayIterator($items);

$exchange = new Element;
$exchange->execute($iterator)
````

## Импорт данных в элемент ИБ

````php
use Sholokhov\Exchange\Source;
use Sholokhov\Exchange\Fields;
use Sholokhov\Exchange\Target\IBlock\Element;

// Ответ от API
$jsonData = json_encode([
    'status' => 'success',
    'data' => [
        [
            'id' => 456,
            'name' => 'Какой-то элемент'
            'author' => 'Иванов'
            'color' => [
                'exterior' => [
                    'id' => 4,
                    'name' => 'Черный'
                ]   
            ],
            'effects' => [
                'effect1',
                'effect2',
                'effect3',
            ], 
        ]   
    ]
]);

$map = [
    (new Fields\Field)
        ->setPath('id')
        ->setCode('XML_ID')
        ->setKeyField(),
    (new Fields\Field)
        ->setPath('name')
        ->setCode('CODE'),
    (new Fields\Field)
        ->setPath('name')
        ->setCode('NAME'),
    (new Fields\IBlock\IBlockElementField)
        ->setPath('author')
        ->setCode('AUTHOR')
        ->setProperty(),
    (new Fields\IBlock\IBlockElementField)
        ->setPath('color.exterior.id')
        ->setCode('COLOR')
        ->setTarget(
            (new Element)
                ->setMap(
                    [
                        (new Fields\Field)
                            ->setPath('color.exterior.id')
                            ->setCode('XML_ID')
                            ->setKeyField(),
                        (new Fields\Field)
                            ->setPath('color.exterior.name')
                            ->setCode('NAME')
                    ]           
                )
        ),
    (new Fields\IBlock\IBlockElementField)
        ->setPath('effects')
        ->setCode('EFFECTS')
        ->setMultiple(true)
        ->setProperty(),        
];

$source = new Source\Json($jsonData, 'data');
$exchange = new Element;
$exchange->setMap($map);
$result = $exchange->execute($source);
````

## Создание нового импорта
Каждый импорт обязан реализовать интерфейс

````
\Sholokhov\Exchange\ExchangeInterface
````

Существует 2 абстрактных класса, которые описывают обмен данными:

````
Sholokhov\Exchange\Application
````

Основной класс обмена данных.

Цель объекта:
* Инициализирует объекта хранения конфигураций обмена
* Инициализация объекта кэширования обмена
* Обработка пользовательской конфигурации обмена

Поддерживает атрибуты наследников класса:
* Sholokhov\Exchange\Target\Attributes\OptionsContainer - Указывается сущность хранения конфигураций
* Sholokhov\Exchange\Target\Attributes\CacheContainer - Указывается сущность хранения кэша обмена

````
Sholokhov\Exchange\AbstractExchange
````
Обмен данных регламентирующий структуру обмена и производящий обработку данных

Цель объекта:
* Задает порядок выполнения обмена
* Получение данных из источника
* Нормализация данных источника данных на основе карты импорта
* Валидация карты импорта
* Запуск "подимпорта" на основе карты
* Поиск существования элемента сущности на основе данных источника
* Добавление в сущность
* Обновление элемента сущности
* Деактивация элементов сущности, которые не пришли в обмене

Поддерживает атрибуты наследников класса:
* Sholokhov\Exchange\Target\Attributes\MapValidator - Валидатор карты обмена

События:
* before_run - Перед запуском обмена
* after_run - После обмена
* after_add - После добавления элемента сущности
* after_update - После обновления сущности
* before_action_item - Перед импортом элемента
* after_action_item - После импорта элемента

### Пример пользовательского обмена

````php
use Sholokhov\Exchange\Messages\Result;
use Sholokhov\Exchange\AbstractExchange;
use Sholokhov\Exchange\Target\Attributes\MapValidator;
use Sholokhov\Exchange\Target\Attributes\OptionsContainer;
use Sholokhov\Exchange\Target\Attributes\CacheContainer;

#[MapValidator('custom map validation')]
#[OptionsContainer('custom options registry')]
#[CacheContainer('custom cache container')]
class Queue extends AbstractExchange
{
    // Обработка параметров обмена
    protected function normalizeOptions(array $options): array
    {
        if (!isset($options['chanel'])) {
            $options['chanel'] = 'default';
        }
        
        return $options;
    }

    // Конфигурация обмена после инициализации конструктора
    protected function configure(): void
    {
        $this->event->subscribeAfterRun([$this, 'checkCount']);
    }

    // Проверка существования элемента в очереди
    protected function exists(array $item): bool
    {
        // Получение свойство, которое отвечает за связь элементов сущности и элементов источника
        $keyField = $this->getKeyField();
        $keyValue = $item[$keyField->getCode()];
        
        if ($this->cache->has($keyValue)) {
            return $this->cache->get($keyValue);
        }
        
        $entity = new DataManager;
        $row = $entity::getRow([
            'filter' => [
                $keyField->getCode() => $keyValue,
                'CHANEL' => $this->getOptions()->get('chanel')
            ],
            'select' => ['ID']
        ]);
        
        if ($row) {
            $this->cache->set($keyValue, (int)$row['ID']);
            return true;
        }
        
        return false;
    }
    
    // Добавление в очередь
    protected function add(array $item): Result
    {
        // ...
    }
    
    // Обновление элемента очереди
    protected function update(array $item): Result
    {
        $keyField = $this->getKeyField();
        $keyValue = $item[$keyField->getCode()];
        $id = $this->cache->get($keyValue);
       
        
        if (!$id) {
            return $this->add($item);
        }
        
        DataManager::add($item);
        
        // ...
    }
    
    // Деактивация старых элементов очереди
    protected function deactivate(): void
    {
        $iterator = DataManager::getList(
            [
                'filter' => [
                    '<DATE_UPDATE' => DateTime::createFromTimestamp($this->timeUp)
                ]
            ]       
        );
        
        // ...
    }
    
    // Действие после обмена
    private function checkCount(): void 
    {
        // ...
    }
}
````
