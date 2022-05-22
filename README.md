<p align="center">
    <a href="https://sylius.com" target="_blank">
        <img src="https://demo.sylius.com/assets/shop/img/logo.png" />
    </a>
</p>

<h1 align="center">Sylius - Newsletter Test</h1>

<p align="center">This is Sylius test project with multiple custom functionalities</p>

About repository
-----

Repository created to fulfill some tests and open source modules / commands [**Marmureanu.ro**](https://marmureanu.ro). 

Functionality list
-----
- [ Newsletter functionality ] A customer can subscribe to multiple newsletters. Admin can manage newsletters and see the list of subscribers. A command ```$ bin/console app:send-newsletter ``` was created to send newsletter to subscribers.

About [ Newsletter functionality ]
-----
- [Send Newsletter Command] - ```$ bin/console app:send-newsletter ``` takes one argument "newsletter_identifier" which can be the [`id`] of the entity or the [`code`] field which is [unique]. If newsletter by id is not found it will try to load the newsletter by code. If newsletter `status` is `FALSE` newsletter will not be send and warning message will be thrown.
- [Send Newsletter Command] - Examples / how to use command ```$ bin/console app:send-newsletter 22 ``` or ```$ bin/console app:send-newsletter fashion ```
- [Send Newsletter Command] - Send-Newsletter command is creating a message for each subscriber and is sending it to queue. It will be then processed async and deliver the message. For Queue Message - Symfony Messenger component is used. With RabbitMq configured. Multiple consumers can be configured to process stored messages.
- [Testing email] - You can use MAILHOG Docker Image or [https://mailtrap.io](https://mailtrap.io)

How to use
------------

```bash
$ bin/console app:send-newsletter fashion
$ bin/console 
```

Authors
-------
[Open to work] Linkedin profile -> [Linkedin_Profile_Link_Marmureanu_Andrei](https://www.linkedin.com/in/andrei-marmureanu-b5334a49/).

Repository owned by [Marmureanu_Andrei_BLOG_MARMUREANU.ro](https://marmureanu.ro/pages/webdesign).

MIT License
-----------

Sylius is completely free and released under the [MIT License](https://github.com/Sylius/Sylius/blob/master/LICENSE).
