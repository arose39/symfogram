Пишу проект с функционлом похожим на инстаграм (загрузка фото, просмотр ленті, подписка/отписка, сервис лайков)

!!!Проект пока в стадии активной разработки и очень сырой, 
но базовый функционал (загрузка фотом, подписки, лайки, 
лента, сжатие фото,фикстуры) уже добавлен.


Для запуска проекта 
 - .env.docker  переименуйте в .env
 - Запустите в консоле комманду docker-compose up
 - потом запустите 
  - docker exec -it  symfogram-php-fpm-1 bash
 - ./bin/console doctrine:database:create
 - ./bin/console doctrine:migrations:migrate
 - ./bin/console doctrine:fixtures:load --append
