<?php

require_once( 'kernel/common/i18n.php' );

class eZLightBoxWrapperType extends eZDataType
{
    const DATA_TYPE_STRING = 'ezlightboxwrapper';

    /*!
     Initializes with a string id and a description.
    */
    function eZLightBoxWrapperType()
    {
        $this->eZDataType( self::DATA_TYPE_STRING, ezi18n( 'datatype/lightboxwrapper', 'Lightbox Wrapper', 'Datatype name' ),
                           array( 'serialize_supported' => true,
                                  'object_serialize_map' => array( 'data_text' => 'text' ) ) );
    }

    /*!
     Sets the default value.
    */
    function initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
        if ( $currentVersion != false )
        {
            $dataText = $originalContentObjectAttribute->attribute( "data_text" );
            $contentObjectAttribute->setAttribute( "data_text", $dataText );
        }
    }

    /*
     Private method, only for using inside this class.
    */
    function validateStringHTTPInput( $data, $contentObjectAttribute, $classAttribute )
    {
        return eZInputValidator::STATE_ACCEPTED;
    }


    /*!
     \reimp
    */
    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
     \reimp
    */
    function validateCollectionAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
     Fetches the http post var string input and stores it in the data instance.
    */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . '_ezlightbox_id_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $id = $http->postVariable( $base . '_ezlightbox_id_' . $contentObjectAttribute->attribute( 'id' ) );
            $contentObjectAttribute->setAttribute( 'data_text', $id );
            return true;
        }
        return false;
    }

    /*!
     Fetches the http post variables for collected information
    */
//     function fetchCollectionAttributeHTTPInput( $collection, $collectionAttribute, $http, $base, $contentObjectAttribute )
//     {
//         if ( $http->hasPostVariable( $base . "_ezlightboxwrapper_data_text_" . $contentObjectAttribute->attribute( "id" ) ) )
//         {
//             $dataText = $http->postVariable( $base . "_ezlightboxwrapper_data_text_" . $contentObjectAttribute->attribute( "id" ) );
//             $collectionAttribute->setAttribute( 'data_text', $dataText );
//             return true;
//         }
//         return false;
//     }

    /*!
     Does nothing since it uses the data_text field in the content object attribute.
     See fetchObjectAttributeHTTPInput for the actual storing.
    */
    function storeObjectAttribute( $attribute )
    {
    }

    /*!
     \reimp
     Simple string insertion is supported.
    */
    function isSimpleStringInsertionSupported()
    {
        return true;
    }

    /*!
     \reimp
     Inserts the string \a $string in the \c 'data_text' database field.
    */
    function insertSimpleString( $object, $objectVersion, $objectLanguage,
                                 $objectAttribute, $string,
                                 &$result )
    {
        $result = array( 'errors' => array(),
                         'require_storage' => true );
        $objectAttribute->setContent( $string );
        $objectAttribute->setAttribute( 'data_text', $string );
        return true;
    }

    function storeClassAttribute( $attribute, $version )
    {
    }

    function storeDefinedClassAttribute( $attribute )
    {
    }

    /*!
     \reimp
    */
    function validateClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
     \reimp
    */
    function fixupClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
    }

    /*!
     \reimp
    */
    function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        return true;
    }

    /*!
     Returns the content.
    */
    function objectAttributeContent( $contentObjectAttribute )
    {
        $id = $contentObjectAttribute->attribute( 'data_text' );
        $lightbox = eZLightbox::fetch( $id );
        return $lightbox;
    }

    /*!
     Returns the meta data used for storing search indeces.
    */
    function metaData( $contentObjectAttribute )
    {
        return false;
    }
    /*!
     \return string representation of an contentobjectattribute data for simplified export

    */
    function toString( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( 'data_text' );
    }

    function fromString( $contentObjectAttribute, $string )
    {
        return $contentObjectAttribute->setAttribute( 'data_text', $string );
    }


    /*!
     Returns the content of the string for use as a title
    */
    function title( $contentObjectAttribute, $name = null )
    {
        $id = $contentObjectAttribute->attribute( 'data_text' );
        $lightbox = eZLightbox::fetch( $id );
        if ( $lightbox )
            return $lightbox->attribute( 'name' );
        else
            return false;
    }

    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        return trim( $contentObjectAttribute->attribute( 'data_text' ) ) != '';
    }

    /*!
     \reimp
    */
    function isIndexable()
    {
        return false;
    }

    /*!
     \reimp
    */
    function isInformationCollector()
    {
        return false;
    }

    /*!
     \reimp
    */
    function sortKey( $contentObjectAttribute )
    {
        $id = $contentObjectAttribute->attribute( 'data_text' );
        $lightbox = eZLightbox::fetch( $id );
        if ( $lightbox )
        {
            $trans = eZCharTransform::instance();
            return $trans->transformByGroup( $lightbox->attribute( 'name' ), 'lowercase' );
        }
    }

    /*!
     \reimp
    */
    function sortKeyType()
    {
        return 'string';
    }

    /*!
      \reimp
    */
    function diff( $old, $new, $options = false )
    {
        $diff = new eZDiff();
        $diff->setDiffEngineType( $diff->engineType( 'text' ) );
        $diff->initDiffEngine();
        $diffObject = $diff->diff( $old->content(), $new->content() );
        return $diffObject;
    }

    /// \privatesection
    /// The max len validator
    public $MaxLenValidator;
}

eZDataType::register( eZLightBoxWrapperType::DATA_TYPE_STRING, 'eZLightBoxWrapperType' );

?>
