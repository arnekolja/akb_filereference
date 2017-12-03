====================
FileReference helper
====================

This extension implements a child class of FileReference and a helper with some convenience methods to help you creating a new FileReference for your model.

Installation
============

Install extension and add its static TypoScript template to your template.

Usage
=====

1. Add TCA and model implementation
-----------------------------------

Add both parts as usual, nothing special here. Example for adding a field named "image":

TCA::

	'image' => [
		'label' => 'LLL:my_extension/Resources/Private/Language/locallang_db.xlf:tx_myextension_domain_model_mymodel.image',
		'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
			'image',
			[
				'maxitems' => 1,
				'foreign_match_fields' => [
					'fieldname' => 'image',
					'tablenames' => 'tx_myextension_domain_model_mymodel',
					'table_local' => 'sys_file',
				],
			],
			'jpg,jpeg,png'
		)
	]

Model::

	/**
	 * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
	 */
	protected $image = null;

	/**
	 * Get image.
	 *
	 * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference
	 */
	public function getImage()
	{
	    return $this->image;
	}

	/**
	 * Set image.
	 *
	 * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $image
	 */
	public function setImage(\TYPO3\CMS\Extbase\Domain\Model\FileReference $image)
	{
	    $this->image = $image;
	}

2. Make use of the helper
-------------------------

In e.g. your controller, you may now use the FileReferenceHelper to move a locally available file to a specific location, returning the resulting FileReference.

Example of handling an upload::

	$myObject = $this->myRepository->findOneByUid($uid);

	if ($_FILES['image'] && $_FILES['image']['size'] > 0) {
		$tmpName = $_FILES['image']['name'];
		$tmpFile = $_FILES['image']['tmp_name'];

		// Get the helper itself.
		$fileReferenceHelper = $this->objectManager->get(FileReferenceHelper::class);

		// Just a convenience method to make sure, it won't result in multiple images
		// where only one is allowed
		$fileReferenceHelper->removeFileReferenceIfEmpty($myObject, "image");

		// 3rd parameter must be a folder within your default storage.
		$fileReference = $fileReferenceHelper->fileToFileReference($tmpFile, $tmpName, "images");

		// As you now have a valid file reference, just add it.
		$myObject->setImage($fileReference);
	}

Notes
=====

* Storage handling defaults to your default storage and has no special handling for sub folders.
