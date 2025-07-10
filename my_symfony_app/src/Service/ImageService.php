<?php

declare(strict_types=1);

namespace App\Service;

use Ramsey\Uuid\Uuid;

class ImageService
{
	private array $allowedExtensions = ["jpeg", "png", "gif"];

	public function saveImage(array $file): string
	{
		if ($file['error'] !== UPLOAD_ERR_OK) {
			throw new \RuntimeException('Ошибка при загрузке файла.');
		}

		$avatarExtension = pathinfo($file["name"], PATHINFO_EXTENSION);
		if (!$this->checkFileExtensions($avatarExtension)) {
			throw new \RuntimeException("Недопустимый формат аватара");
		}

		$newAvatarName = $this->generateAvatarFilename($avatarExtension);
		$this->moveUserAvatar($file["tmp_name"], $newAvatarName);

		return $newAvatarName;
	}

	public function deleteImage(string $filePath): bool
	{
		$destination = "uploads/" . $filePath;
		if (!file_exists($destination)) {
			throw new \RuntimeException("Удаляемый файл не найден!");
		}
		
		return unlink($destination);
	}

	private function checkFileExtensions(string $ext): bool
	{
		return in_array($ext, $this->allowedExtensions);
	}

	private function generateAvatarFilename(string $extension): string
	{
		$randomUuid = Uuid::uuid4();
		return "avatar{$randomUuid}" . "." . $extension;
	}

	private function moveUserAvatar(string $path, string $filename)
	{
		$uploadDir = "uploads/" . $filename;
		echo ($path);
		if (!move_uploaded_file($path, $uploadDir)) {
			throw new \RuntimeException("Ошибка сохранении аватара!");
		}
	}
}
