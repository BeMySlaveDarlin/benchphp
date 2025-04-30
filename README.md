# PHP Benchmark Project

Проект для сравнения производительности PHP на разных железках и операционках.

Текущие кейсы:

- Генерация JSON (Hello World)
- Чтение/запись файла
- Чтение/запись в PostgreSQL
- Нагрузку по памяти

Используются Docker, Composer, Make, .env и простейший DI-контейнер.

## Требования

- Docker + Docker Compose
- GNU Make

## Установка

1. Клонировать репозиторий:
```bash
    git clone https://github.com/bemyslavedarlin/benchphp.git
    cd benchphp
```
2. Заполнить настройки:
```bash
  cp .env.example .env
```
3. Собрать проект:
```bash
    make
    # or
    make build
    make up
    make composer-instal
```

## Тесты

### Запуск тестов
```bash
    make benchmark HelloWorldBenchmark
    # or for all
    make benchmarks
```

### Расширешие тесткейсов

* Реализация от интерфейса

```php
interface BenchmarkInterface {
    public function handle(array $options = []): array;
}
```

* ПР в репозиторий
