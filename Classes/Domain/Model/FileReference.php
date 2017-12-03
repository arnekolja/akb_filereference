<?php

namespace Akb\FileReference\Domain\Model;

/**
 * Class FileReference
 */
class FileReference extends \TYPO3\CMS\Extbase\Domain\Model\FileReference {

	/**
	 * setFile
	 *
	 * @param \TYPO3\CMS\Core\Resource\File $falFile
	 * @return void
	 */
	public function setFile(\TYPO3\CMS\Core\Resource\File $falFile) {
		$this->uidLocal = (int) $falFile->getUid();
	}

}
