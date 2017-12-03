<?php

namespace Akb\FileReference\Helper;

use Akb\FileReference\Domain\Model\FileReference;
use TYPO3\CMS\Core\Resource\ResourceFactory;

class FileReferenceHelper {

	/**
	 * Object Manager
	 *
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
	 * @inject
	 */
	protected $objectManager;

	/**
	 * Makes a file reference out of a file at a given path. Primary usage: Handling uploaded file.
	 *
	 * @param  string $localFilePath Full qualified file path with folder and file
	 * @param  string $localFileName The filename only
	 * @param  string $targetFolder  A target folder within default storage
	 * @return \Akb\FileReference\Domain\Model\FileReference
	 */
	public function fileToFileReference($localFilePath, $localFileName, $targetFolder='/') {
		$resourceFactory = ResourceFactory::getInstance();
		$storage = $resourceFactory->getDefaultStorage();

		if (!$storage->hasFolder($targetFolder)) {
			$storage->createFolder($targetFolder);
		}

		// Add file to the storage.
		$newFile = $storage->addFile(
			$localFilePath,
			$storage->getFolder($targetFolder),
			$localFileName
		);

		// Actually add a file reference to the member
		$fileReference = $this->objectManager->get(FileReference::class);
		$fileReference->setFile($newFile);

		return $fileReference;
	}

	/**
	 * Remove a property's file reference (remove file from object), and check whether the property is empty before
	 * @param  mixed  $object       The object which has the property
	 * @param  string $propertyName Name of the property which shall be cleared
	 * @return void
	 */
	public function removeFileReferenceIfEmpty($object, $propertyName) {
		$getterName = $this->getGetterName($propertyName);

		if (!empty($object->$getterName())) {
			$this->removeFileReference($object, $propertyName);
		}
	}

	/**
	 * Remove a property's file reference (remove file from object)
	 * @param  mixed  $object       The object which has the property
	 * @param  string $propertyName Name of the property which shall be cleared
	 * @return void
	 */
	public function removeFileReference($object, $propertyName) {
		$getterName = $this->getGetterName($propertyName);

		$resourceFactory     = ResourceFactory::getInstance();
		$fileReferenceObject = $resourceFactory->getFileReferenceObject($object->$getterName()->getUid());
		$fileWasDeleted      = $fileReferenceObject->getOriginalFile()->delete();
	}

	/**
	 * Helper function to get the name of the getter for a property.
	 *
	 * @param  string $propertyName The property name
	 * @return string Getter name
	 */
	private function getGetterName($propertyName) {
		return "get" . ucfirst($propertyName);
	}

}
