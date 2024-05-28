<?php

// Токен вашего бота, который вы получили от BotFather в Telegram
define('BOT_TOKEN', '7096009018:AAGFK2B52--wkri2ZRBBUtff35wq66oA6XQ');

// URL-адрес вашего веб-хука, куда будут отправляться обновления от Telegram
define('WEBHOOK_URL', 'https://raw.githubusercontent.com/johns1lver/SynthInnovate/32e095a1ba56b242926555daa7db5b15b3320190/bot.php');

// API ключ для ChatGPT
define('CHATGPT_API_KEY', 'sk-proj-HKplGZi4SaJyMRODm6maT3BlbkFJCkqAJFZPphxLZDBf66lt');

// Функция для отправки сообщения пользователю
function sendMessage($chat_id, $message) {
    $url = "https://api.telegram.org/bot" . BOT_TOKEN . "/sendMessage";
    $params = [
        'chat_id' => $chat_id,
        'text' => $message
    ];
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_exec($ch);
    curl_close($ch);
}

// Функция для проверки длины сообщения
function checkMessageLength($chat_id, $message) {
    if (str_word_count($message) < 3) {
        sendMessage($chat_id, "Пожалуйста, напишите чуть больше.");
        return false;
    }
    return true;
}

// Функция для обработки команды /start
function start($chat_id) {
    $response = "Привет! Я бот для секстинга. Я могу вести разговор на 18+ темы. "
                . "Вот список доступных команд:\n"
                . "/character - изменить настройки персонажа\n"
                . "/send - начать общение";
    sendMessage($chat_id, $response);
}

// Функция для обработки команды /character
function setCharacter($chat_id) {
    // Начинаем задавать вопросы для настройки персонажа
    sendMessage($chat_id, "Давайте начнем настройку персонажа.");
    sendMessage($chat_id, "Опишите внешность персонажа. Например, его/ее пол, возраст и т.д.");
}

// Функция для обработки команды /yes
function processYes($chat_id) {
    // Реализуйте здесь логику подтверждения изменения настроек персонажа
}

// Функция для обработки команды /no
function processNo($chat_id) {
    // Реализуйте здесь логику отказа от изменения настроек персонажа
}

// Функция для обработки команды /send
function processSend($chat_id) {
    // Реализуйте здесь логику начала общения
}

// Функция для обработки ответов на вопросы о персонаже
function processCharacterSettings($chat_id, $message) {
    // В этой функции можно добавить логику сохранения ответа пользователя и перехода к следующему вопросу
    // Например, можно использовать базу данных для сохранения настроек
    // После сохранения ответа, можно задать следующий вопрос или завершить настройку
    // В данном примере просто отправляем сообщение с подтверждением
    sendMessage($chat_id, "Спасибо! Ваш ответ сохранен.");
    // Задаем следующий вопрос или завершаем настройку
    // Для примера, следующий вопрос можно задать через некоторое время с помощью задержки
    // sendMessage($chat_id, "Опишите характер и предпочтения в общении.");
    // Или можно реализовать цикл с вопросами и сохранением ответов
}

// Обработка ответов пользователя на вопросы о персонаже
if (isset($update["message"]["reply_to_message"])) {
    $chat_id = $update["message"]["chat"]["id"];
    $reply_to_message = $update["message"]["reply_to_message"];
    $text = $update["message"]["text"];
    
    // Проверяем, отвечает ли пользователь на один из вопросов о персонаже
    if (strpos($reply_to_message["text"], "Опишите внешность персонажа") !== false) {
        processCharacterSettings($chat_id, $text);
        // Задаем следующий вопрос
        sendMessage($chat_id, "Опишите характер и предпочтения в общении.");
    } elseif (strpos($reply_to_message["text"], "Опишите характер и предпочтения в общении") !== false) {
        processCharacterSettings($chat_id, $text);
        // Задаем следующий вопрос
        sendMessage($chat_id, "Опишите ваши любимые темы для общения.");
    } elseif (strpos($reply_to_message["text"], "Опишите ваши любимые темы для общения") !== false) {
        processCharacterSettings($chat_id, $text);
        // Можно завершить настройку или задать дополнительные вопросы
        sendMessage($chat_id, "Настройка персонажа завершена.");
    }
}


// Функция для отправки запроса к API ChatGPT и получения ответа
function generateChatGPTResponse($message) {
    $url = "https://api.openai.com/v1/engines/text-davinci-003/completions";
    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . CHATGPT_API_KEY
    ];
    $data = [
        'prompt' => $message,
        'max_tokens' => 50
    ];
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true)['choices'][0]['text'];
}

// Установка веб-хука для получения обновлений от Telegram
function setWebhook() {
    $url = "https://api.telegram.org/bot" . BOT_TOKEN . "/setWebhook";
    $params = [
        'url' => WEBHOOK_URL
    ];
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_exec($ch);
    curl_close($ch);
}

// Функция для обработки сообщений пользователя
function processMessage($chat_id, $text) {
    switch ($text) {
        case '/start':
            start($chat_id);
            break;
        case '/character':
            setCharacter($chat_id);
            break;
        case '/yes':
            processYes($chat_id);
            break;
        case '/no':
            processNo($chat_id);
            break;
        case '/send':
            processSend($chat_id);
            break;
        default:
            // Проверяем, является ли сообщение ответом на вопрос о персонаже
            // Например, если ожидается описание внешности, характера или любимых тем
            // Обработка ответа может быть реализована здесь
            break;
    }
}


// Установка веб-хука при первом запуске скрипта
setWebhook();

// Получение обновлений от Telegram
$update = json_decode(file_get_contents('php://input'), true);

// Обработка команд пользователя
if (isset($update["message"])) {
    $chat_id = $update["message"]["chat"]["id"];
    $text = $update["message"]["text"];

    switch ($text) {
        case '/start':
            start($chat_id);
            break;
        case '/character':
            setCharacter($chat_id);
            break;
        case '/yes':
            processYes($chat_id);
            break;
        case '/no':
            processNo($chat_id);
            break;
        case '/send':
            processSend($chat_id);
            break;
        default:
            if (strpos($text, '/') === false) {
                // Обрабатываем общение с ChatGPT
                $response = generateChatGPTResponse($text);
                sendMessage($chat_id, $response);
            }
            break;
    }
}
?>
