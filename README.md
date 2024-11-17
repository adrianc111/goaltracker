## URL
https://goaltracker.co

![screen_3](https://github.com/user-attachments/assets/54b6eef8-1b3f-42e4-8c48-25e89d27acf8)

## Local Setup (docker)

1. Install docker
2. Install composer
3. `cp .env.example .env`
4. Run `./vendor/bin/sail up`
5. `./vendor/bin/sail artisan key:generate`
6. Run migrations `./vendor/bin/sail artisan migrate --seed`
7. Run npm ci `./vendor/bin/sail npm ci`
8. Run npm watcher `./vendor/bin/sail npm run dev`


## xDebug Settings
![img.png](img.png)
