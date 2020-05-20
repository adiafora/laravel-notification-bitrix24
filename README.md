Laravel Notification to Bitrix24
=====================

Данный пакет расширяет стандартные возможности Notification Laravel, позволяя отправлять уведомления из Вашего приложения в чат Битрикс24, или же пользователю Битрикс24. 

Для реализации уведомлений используется система вебхуков на основании REST API Битрикс24 (подробнее о вебхуках читайте в официальной документации [Битрикс24](https://dev.1c-bitrix.ru/learning/course/?COURSE_ID=99&LESSON_ID=8581)). Это значит, что Вы должны самостоятельно добавить вебхук на своем портале Битрикс24 и получить его токен. Данный токен необходимо внести в файл конфигурации `config/bitrix24_notice.php`. 
Также обязательно в файл конфигурации внести ID пользователя Битрикс24, от имени которого будут отправляться уведомления, и поддомен Вашей компании в Битрикс24.


Установка
-----------------------------------

Установка пакета с помощью Composer.

```php
    composer require "adiafora/laravel-notification-bitrix24"
```
Если версия Laravel меньше чем 5.5 - добавьте в файл `config/app.php` вашего проекта в конец массива `providers`:
```php
    Adiafora\Bitrix24\Bitrix24ServiceProvider::class,
```

Конфигурация
-----------------------------------

После установки, выполните в консоли команду публикации файла конфигурации: 

```php
    php artisan vendor:publish --provider="Adiafora\Bitrix24\Bitrix24ServiceProvider"
```

В нем Вы обязательно должны  заполнить все поля, описанные выше, иначе уведомления не будут работать.

Использование
-----------------------------------

В Вашем `via()` Вы можете использовать канал:

```php
   use Adiafora\Bitrix24\Bitrix24Channel;
   use Adiafora\Bitrix24\Bitrix24Message;
   use Illuminate\Notifications\Notification;
   
   class BitrixNotice extends Notification
   {
       protected $invoice;
       
       public function __construct($invoice)
       {
           $this->invoice = $invoice;
       }
       
       public function via($notifiable)
       {
           return [Bitrix24Channel::class];
       }
   
       public function toBitrix24($notifiable)
       {
           $data = [
               'invoice' => $this->invoice,
           ];
           
           return (new Bitrix24Message)
                       ->view('notice', $data)
                       ->toUser();
       }
   }
```

Пакет ожидает, что ему будет передан ID чата, в который необходимо отправить сообщение, или же ID уведомляемого пользователя.

Например, если ID пользователя Битрикс24 - `56`, создать уведомление для него можно вот так:

```php
    Notification::send(56, new BitrixNotice($invoice));
```

или же

```php
    Notification::route('bitrix24', '56')
                ->notify(new BitrixNotice($invoice));
```

Если же для уведомления Вы используете данные Вашей модели, то Вы должны добавить следующую функцию в уведомляемую модель, которая должна вернуть число - ID чата или пользователя:

```php
    public function routeNotificationForBitrix24(): int
    {
        return $this->bitrix_id;
    }
```

### Доступные методы

`view()` В качестве уведомления Вы можете использовать шаблон Blade. Метод принимает название шаблона и массив с данными, которые будут использованы в шаблоне. При использовании шаблона Вы все-равно должны использовать форматирование, описанное в [документации REST API](https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=93&LESSON_ID=7679)

`text()` Простой текст уведомления

`toUser()` По-умолчанию уведомления отправляются в чат, ID которого был передан. Если же Вы хотите отправить сообщение пользователю, то Вы должны передать его ID, и у объекта `new Bitrix24Message()` вызвать метод `toUser()`. Таким образом, данный метод определяет, к чему относится переданный ID - к чату, или пользователю.

Лицензия
-----------------------------------

MIT Лицензия (MIT). Свободно распространяемый продукт.