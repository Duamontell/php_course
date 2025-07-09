<?php

declare(strict_types=1);

namespace App\Service;

use Ramsey\Uuid\Uuid;

class ImageService
{
	private array $allowedExtensions = ["jpeg", "png", "gif"];

	public function saveUserAvatar(array $file): string
	{
		if ($file['error'] !== UPLOAD_ERR_OK) {
			throw new \RuntimeException('Ошибка при загрузке файла!');
		}

		$avatarExtension = pathinfo($file["name"], PATHINFO_EXTENSION);
		if (!$this->checkFileExtensions($avatarExtension)) {
			throw new \RuntimeException("Недопустимый формат изображения!");
		}

		$newFileName = $this->generateAvatarFilename($avatarExtension);
		$this->moveUserAvatar($file["tmp_name"], $newFileName);

		return $newFileName;
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
		if (!move_uploaded_file($path, $uploadDir)) {
			throw new \RuntimeException("Ошибка сохранении изображения!");
		}
	}
}
