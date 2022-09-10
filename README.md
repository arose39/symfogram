Пишу проект с функционлом похожим на инстаграм (загрузка фото, просмотр ленті, подписка/отписка, сервис лайков)

!!!Проект пока в стадии активной разработки и очень сырой, 
но базовый функционал (загрузка фотом, подписки, лайки, 
лента, сжатие фото,фикстуры) уже добавлен.

Но пока без рефакторинга. Проэкт пока запускаю на опенсервере, докер компоуз доработаю позже.

.env.example  should rename-> .env
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
php bin/console doctrine:fixtures:load --append



