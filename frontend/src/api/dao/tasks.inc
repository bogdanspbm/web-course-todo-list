<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/credentials/postgres_credentials.inc';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/database/database.inc';


class TasksDao
{
    private $connection;

    function __construct()
    {
        $postgres = new PostgresSQL(DB_LOGIN, DB_PASSWORD, "todo");
        $this->connection = $postgres->getConnection();
    }

    public function getFile($uid, $email) {
        $sql = "SELECT file_name, file_data FROM tasks WHERE uid = ? AND user_mail = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$uid, $email]);
        $file = $stmt->fetch(PDO::FETCH_ASSOC);
        return $file ? $file : false;
    }

    public function getTasks($email)
    {
        $stmt = $this->connection->prepare("SELECT *, EXTRACT(EPOCH FROM task_date) as task_date_long FROM todo.public.tasks WHERE tasks.user_mail = ? AND tasks.is_draft = FALSE ORDER BY task_date");
        $stmt->execute([$email]);
        $tasks = $stmt->fetchAll();
        return $tasks;
    }

    public function getDraft($email)
    {
        $stmt = $this->connection->prepare("SELECT *, EXTRACT(EPOCH FROM task_date) as task_date_long FROM todo.public.tasks WHERE tasks.user_mail = ? AND tasks.is_draft = TRUE ORDER BY task_date");
        $stmt->execute([$email]);
        $tasks = $stmt->fetchAll();
        return $tasks;
    }

    public function updateFile($uid, $email, $fileName, $fileData) {
        // Подготовка SQL запроса для обновления информации о файле в задаче
        $sql = "UPDATE todo.public.tasks SET file_name = ?, file_data = ? WHERE uid = ? AND user_mail = ?";
        $stmt = $this->connection->prepare($sql);

        // Конвертация данных файла в подходящий формат для БД, если это необходимо
        // Предположим, что file_data - это BLOB поле в вашей БД
        $blobData = fopen($fileData, 'rb');

        // Выполнение запроса
        $stmt->bindParam(1, $fileName);
        $stmt->bindParam(2, $blobData, PDO::PARAM_LOB);
        $stmt->bindParam(3, $uid);
        $stmt->bindParam(4, $email);
        $result = $stmt->execute();

        // Закрытие файла после использования
        fclose($blobData);

        return $result;
    }


    public function getTask($email, $uid)
    {
        $stmt = $this->connection->prepare("SELECT *, EXTRACT(EPOCH FROM task_date) as task_date_long FROM todo.public.tasks WHERE tasks.uid = ? AND tasks.user_mail = ? ORDER BY task_date");
        $stmt->execute([$uid, $email]);
        $task = $stmt->fetch();
        return $task;
    }

    public function hasTask($email, $uid)
    {
        $stmt = $this->connection->prepare("SELECT *, EXTRACT(EPOCH FROM task_date) as task_date_long FROM todo.public.tasks WHERE tasks.uid = ? AND tasks.user_mail = ? ORDER BY task_date");
        $stmt->execute([$uid, $email]);
        $task = $stmt->fetchAll();
        return count($task) != 0;
    }

    public function createDraft($uid, $userMail)
    {
        $stmt = $this->connection->prepare("INSERT INTO todo.public.tasks (uid, user_mail, is_draft) VALUES (?,?, TRUE)");
        $stmt->execute([$uid, $userMail]);
        $task = $stmt->fetch();
        return $task;
    }


    public function updateTitle($title, $entityUID, $email)
    {
        $stmt = $this->connection->prepare("UPDATE todo.public.tasks SET title = ? WHERE uid = ? AND user_mail = ?");
        $stmt->execute([$title, $entityUID, $email]);
        return true;
    }

    public function updateDescription($description, $entityUID, $email)
    {
        $stmt = $this->connection->prepare("UPDATE todo.public.tasks SET description = ? WHERE uid = ? AND user_mail = ?");
        $stmt->execute([$description, $entityUID, $email]);
        return true;
    }

    public function updatePriority($priority, $entityUID, $email)
    {
        $stmt = $this->connection->prepare("UPDATE todo.public.tasks SET priority = ? WHERE uid = ? AND user_mail = ?");
        $stmt->execute([$priority, $entityUID, $email]);
        return true;
    }

    public function updateDate($date, $entityUID, $email)
    {
        $stmt = $this->connection->prepare("UPDATE todo.public.tasks SET task_date = to_timestamp(?) WHERE uid = ? AND user_mail = ?");
        $stmt->execute([$date, $entityUID, $email]);
        return true;
    }

    public function updateColor($color, $entityUID, $email)
    {
        $stmt = $this->connection->prepare("UPDATE todo.public.tasks SET color = ? WHERE uid = ? AND user_mail = ?");
        $stmt->execute([$color, $entityUID, $email]);
        return true;
    }


    public function publishTask($taskUID, $email)
    {
        $stmt = $this->connection->prepare("UPDATE todo.public.tasks SET is_draft = FALSE WHERE uid = ? AND user_mail = ?");
        $stmt->execute([$taskUID, $email]);
        return true;
    }

    public function createTask($entity)
    {
        $stmt = $this->connection->prepare("INSERT INTO todo.public.tasks (uid, title, description, task_date, priority, color, user_mail, file_name, file_data, is_draft) VALUES (?, ?, ?, to_timestamp(?), ?, ?, ?, ?, ?, FALSE)");
        $stmt->execute([
            $entity['uid'],
            $entity['title'],
            $entity['description'],
            $entity['task_date'],
            $entity['priority'],
            $entity['color'],
            $entity['user_mail'],
            $entity['file_name'],
            $entity['file_data']
        ]);
        $task = $stmt->fetch();
        return $task;
    }

    public function deleteTask($taskUID, $email)
    {
        $stmt = $this->connection->prepare("DELETE FROM todo.public.tasks WHERE uid = ? AND user_mail = ?");
        $stmt->execute([$taskUID, $email]);
        $task = $stmt->fetch();
        return $task;
    }

}