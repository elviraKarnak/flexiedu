<?php

// Functions and constants

namespace {
    if(!function_exists('\\htmlpurifier_filter_extractstyleblocks_muteerrorhandler')){
        function htmlpurifier_filter_extractstyleblocks_muteerrorhandler(...$args) {
            return \learndash_reports_htmlpurifier_filter_extractstyleblocks_muteerrorhandler(...func_get_args());
        }
    }

}


namespace LearnDash\Reports {

    class AliasAutoloader
    {
        private string $includeFilePath;

        private array $autoloadAliases = array (
  'Composer\\Pcre\\MatchAllResult' => 
  array (
    'type' => 'class',
    'classname' => 'MatchAllResult',
    'isabstract' => false,
    'namespace' => 'Composer\\Pcre',
    'extends' => 'LearnDash\\Reports\\Composer\\Pcre\\MatchAllResult',
    'implements' => 
    array (
    ),
  ),
  'Composer\\Pcre\\MatchAllStrictGroupsResult' => 
  array (
    'type' => 'class',
    'classname' => 'MatchAllStrictGroupsResult',
    'isabstract' => false,
    'namespace' => 'Composer\\Pcre',
    'extends' => 'LearnDash\\Reports\\Composer\\Pcre\\MatchAllStrictGroupsResult',
    'implements' => 
    array (
    ),
  ),
  'Composer\\Pcre\\MatchAllWithOffsetsResult' => 
  array (
    'type' => 'class',
    'classname' => 'MatchAllWithOffsetsResult',
    'isabstract' => false,
    'namespace' => 'Composer\\Pcre',
    'extends' => 'LearnDash\\Reports\\Composer\\Pcre\\MatchAllWithOffsetsResult',
    'implements' => 
    array (
    ),
  ),
  'Composer\\Pcre\\MatchResult' => 
  array (
    'type' => 'class',
    'classname' => 'MatchResult',
    'isabstract' => false,
    'namespace' => 'Composer\\Pcre',
    'extends' => 'LearnDash\\Reports\\Composer\\Pcre\\MatchResult',
    'implements' => 
    array (
    ),
  ),
  'Composer\\Pcre\\MatchStrictGroupsResult' => 
  array (
    'type' => 'class',
    'classname' => 'MatchStrictGroupsResult',
    'isabstract' => false,
    'namespace' => 'Composer\\Pcre',
    'extends' => 'LearnDash\\Reports\\Composer\\Pcre\\MatchStrictGroupsResult',
    'implements' => 
    array (
    ),
  ),
  'Composer\\Pcre\\MatchWithOffsetsResult' => 
  array (
    'type' => 'class',
    'classname' => 'MatchWithOffsetsResult',
    'isabstract' => false,
    'namespace' => 'Composer\\Pcre',
    'extends' => 'LearnDash\\Reports\\Composer\\Pcre\\MatchWithOffsetsResult',
    'implements' => 
    array (
    ),
  ),
  'Composer\\Pcre\\PHPStan\\InvalidRegexPatternRule' => 
  array (
    'type' => 'class',
    'classname' => 'InvalidRegexPatternRule',
    'isabstract' => false,
    'namespace' => 'Composer\\Pcre\\PHPStan',
    'extends' => 'LearnDash\\Reports\\Composer\\Pcre\\PHPStan\\InvalidRegexPatternRule',
    'implements' => 
    array (
      0 => 'PHPStan\\Rules\\Rule',
    ),
  ),
  'Composer\\Pcre\\PHPStan\\PregMatchFlags' => 
  array (
    'type' => 'class',
    'classname' => 'PregMatchFlags',
    'isabstract' => false,
    'namespace' => 'Composer\\Pcre\\PHPStan',
    'extends' => 'LearnDash\\Reports\\Composer\\Pcre\\PHPStan\\PregMatchFlags',
    'implements' => 
    array (
    ),
  ),
  'Composer\\Pcre\\PHPStan\\PregMatchParameterOutTypeExtension' => 
  array (
    'type' => 'class',
    'classname' => 'PregMatchParameterOutTypeExtension',
    'isabstract' => false,
    'namespace' => 'Composer\\Pcre\\PHPStan',
    'extends' => 'LearnDash\\Reports\\Composer\\Pcre\\PHPStan\\PregMatchParameterOutTypeExtension',
    'implements' => 
    array (
      0 => 'PHPStan\\Type\\StaticMethodParameterOutTypeExtension',
    ),
  ),
  'Composer\\Pcre\\PHPStan\\PregMatchTypeSpecifyingExtension' => 
  array (
    'type' => 'class',
    'classname' => 'PregMatchTypeSpecifyingExtension',
    'isabstract' => false,
    'namespace' => 'Composer\\Pcre\\PHPStan',
    'extends' => 'LearnDash\\Reports\\Composer\\Pcre\\PHPStan\\PregMatchTypeSpecifyingExtension',
    'implements' => 
    array (
      0 => 'PHPStan\\Type\\StaticMethodTypeSpecifyingExtension',
      1 => 'PHPStan\\Analyser\\TypeSpecifierAwareExtension',
    ),
  ),
  'Composer\\Pcre\\PHPStan\\PregReplaceCallbackClosureTypeExtension' => 
  array (
    'type' => 'class',
    'classname' => 'PregReplaceCallbackClosureTypeExtension',
    'isabstract' => false,
    'namespace' => 'Composer\\Pcre\\PHPStan',
    'extends' => 'LearnDash\\Reports\\Composer\\Pcre\\PHPStan\\PregReplaceCallbackClosureTypeExtension',
    'implements' => 
    array (
      0 => 'PHPStan\\Type\\StaticMethodParameterClosureTypeExtension',
    ),
  ),
  'Composer\\Pcre\\PHPStan\\UnsafeStrictGroupsCallRule' => 
  array (
    'type' => 'class',
    'classname' => 'UnsafeStrictGroupsCallRule',
    'isabstract' => false,
    'namespace' => 'Composer\\Pcre\\PHPStan',
    'extends' => 'LearnDash\\Reports\\Composer\\Pcre\\PHPStan\\UnsafeStrictGroupsCallRule',
    'implements' => 
    array (
      0 => 'PHPStan\\Rules\\Rule',
    ),
  ),
  'Composer\\Pcre\\PcreException' => 
  array (
    'type' => 'class',
    'classname' => 'PcreException',
    'isabstract' => false,
    'namespace' => 'Composer\\Pcre',
    'extends' => 'LearnDash\\Reports\\Composer\\Pcre\\PcreException',
    'implements' => 
    array (
    ),
  ),
  'Composer\\Pcre\\Preg' => 
  array (
    'type' => 'class',
    'classname' => 'Preg',
    'isabstract' => false,
    'namespace' => 'Composer\\Pcre',
    'extends' => 'LearnDash\\Reports\\Composer\\Pcre\\Preg',
    'implements' => 
    array (
    ),
  ),
  'Composer\\Pcre\\Regex' => 
  array (
    'type' => 'class',
    'classname' => 'Regex',
    'isabstract' => false,
    'namespace' => 'Composer\\Pcre',
    'extends' => 'LearnDash\\Reports\\Composer\\Pcre\\Regex',
    'implements' => 
    array (
    ),
  ),
  'Composer\\Pcre\\ReplaceResult' => 
  array (
    'type' => 'class',
    'classname' => 'ReplaceResult',
    'isabstract' => false,
    'namespace' => 'Composer\\Pcre',
    'extends' => 'LearnDash\\Reports\\Composer\\Pcre\\ReplaceResult',
    'implements' => 
    array (
    ),
  ),
  'Composer\\Pcre\\UnexpectedNullMatchException' => 
  array (
    'type' => 'class',
    'classname' => 'UnexpectedNullMatchException',
    'isabstract' => false,
    'namespace' => 'Composer\\Pcre',
    'extends' => 'LearnDash\\Reports\\Composer\\Pcre\\UnexpectedNullMatchException',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Arborize' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Arborize',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Arborize',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrCollections' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrCollections',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrCollections',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef',
    'isabstract' => true,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_CSS' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_CSS',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_CSS',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_CSS_AlphaValue' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_CSS_AlphaValue',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_CSS_AlphaValue',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_CSS_Background' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_CSS_Background',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_CSS_Background',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_CSS_BackgroundPosition' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_CSS_BackgroundPosition',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_CSS_BackgroundPosition',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_CSS_Border' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_CSS_Border',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_CSS_Border',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_CSS_Color' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_CSS_Color',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_CSS_Color',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_CSS_Composite' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_CSS_Composite',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_CSS_Composite',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_CSS_DenyElementDecorator' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_CSS_DenyElementDecorator',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_CSS_DenyElementDecorator',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_CSS_Filter' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_CSS_Filter',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_CSS_Filter',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_CSS_Font' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_CSS_Font',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_CSS_Font',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_CSS_FontFamily' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_CSS_FontFamily',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_CSS_FontFamily',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_CSS_Ident' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_CSS_Ident',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_CSS_Ident',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_CSS_ImportantDecorator' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_CSS_ImportantDecorator',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_CSS_ImportantDecorator',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_CSS_Length' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_CSS_Length',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_CSS_Length',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_CSS_ListStyle' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_CSS_ListStyle',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_CSS_ListStyle',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_CSS_Multiple' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_CSS_Multiple',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_CSS_Multiple',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_CSS_Number' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_CSS_Number',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_CSS_Number',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_CSS_Percentage' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_CSS_Percentage',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_CSS_Percentage',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_CSS_Ratio' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_CSS_Ratio',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_CSS_Ratio',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_CSS_TextDecoration' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_CSS_TextDecoration',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_CSS_TextDecoration',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_CSS_URI' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_CSS_URI',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_CSS_URI',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_Clone' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_Clone',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_Clone',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_Enum' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_Enum',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_Enum',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_HTML_Bool' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_HTML_Bool',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_HTML_Bool',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_HTML_Class' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_HTML_Class',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_HTML_Class',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_HTML_Color' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_HTML_Color',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_HTML_Color',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_HTML_ContentEditable' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_HTML_ContentEditable',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_HTML_ContentEditable',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_HTML_FrameTarget' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_HTML_FrameTarget',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_HTML_FrameTarget',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_HTML_ID' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_HTML_ID',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_HTML_ID',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_HTML_Length' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_HTML_Length',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_HTML_Length',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_HTML_LinkTypes' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_HTML_LinkTypes',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_HTML_LinkTypes',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_HTML_MultiLength' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_HTML_MultiLength',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_HTML_MultiLength',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_HTML_Nmtokens' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_HTML_Nmtokens',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_HTML_Nmtokens',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_HTML_Pixels' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_HTML_Pixels',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_HTML_Pixels',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_Integer' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_Integer',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_Integer',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_Lang' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_Lang',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_Lang',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_Switch' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_Switch',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_Switch',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_Text' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_Text',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_Text',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_URI' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_URI',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_URI',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_URI_Email' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_URI_Email',
    'isabstract' => true,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_URI_Email',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_URI_Email_SimpleCheck' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_URI_Email_SimpleCheck',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_URI_Email_SimpleCheck',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_URI_Host' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_URI_Host',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_URI_Host',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_URI_IPv4' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_URI_IPv4',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_URI_IPv4',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrDef_URI_IPv6' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrDef_URI_IPv6',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrDef_URI_IPv6',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrTransform' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrTransform',
    'isabstract' => true,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrTransform',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrTransform_Background' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrTransform_Background',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrTransform_Background',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrTransform_BdoDir' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrTransform_BdoDir',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrTransform_BdoDir',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrTransform_BgColor' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrTransform_BgColor',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrTransform_BgColor',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrTransform_BoolToCSS' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrTransform_BoolToCSS',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrTransform_BoolToCSS',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrTransform_Border' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrTransform_Border',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrTransform_Border',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrTransform_EnumToCSS' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrTransform_EnumToCSS',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrTransform_EnumToCSS',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrTransform_ImgRequired' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrTransform_ImgRequired',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrTransform_ImgRequired',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrTransform_ImgSpace' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrTransform_ImgSpace',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrTransform_ImgSpace',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrTransform_Input' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrTransform_Input',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrTransform_Input',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrTransform_Lang' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrTransform_Lang',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrTransform_Lang',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrTransform_Length' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrTransform_Length',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrTransform_Length',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrTransform_Name' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrTransform_Name',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrTransform_Name',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrTransform_NameSync' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrTransform_NameSync',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrTransform_NameSync',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrTransform_Nofollow' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrTransform_Nofollow',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrTransform_Nofollow',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrTransform_SafeEmbed' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrTransform_SafeEmbed',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrTransform_SafeEmbed',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrTransform_SafeObject' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrTransform_SafeObject',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrTransform_SafeObject',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrTransform_SafeParam' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrTransform_SafeParam',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrTransform_SafeParam',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrTransform_ScriptRequired' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrTransform_ScriptRequired',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrTransform_ScriptRequired',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrTransform_TargetBlank' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrTransform_TargetBlank',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrTransform_TargetBlank',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrTransform_TargetNoopener' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrTransform_TargetNoopener',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrTransform_TargetNoopener',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrTransform_TargetNoreferrer' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrTransform_TargetNoreferrer',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrTransform_TargetNoreferrer',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrTransform_Textarea' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrTransform_Textarea',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrTransform_Textarea',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrTypes' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrTypes',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrTypes',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_AttrValidator' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_AttrValidator',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_AttrValidator',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Bootstrap' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Bootstrap',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Bootstrap',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_CSSDefinition' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_CSSDefinition',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_CSSDefinition',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_ChildDef' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_ChildDef',
    'isabstract' => true,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_ChildDef',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_ChildDef_Chameleon' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_ChildDef_Chameleon',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_ChildDef_Chameleon',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_ChildDef_Custom' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_ChildDef_Custom',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_ChildDef_Custom',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_ChildDef_Empty' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_ChildDef_Empty',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_ChildDef_Empty',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_ChildDef_List' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_ChildDef_List',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_ChildDef_List',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_ChildDef_Optional' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_ChildDef_Optional',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_ChildDef_Optional',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_ChildDef_Required' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_ChildDef_Required',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_ChildDef_Required',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_ChildDef_StrictBlockquote' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_ChildDef_StrictBlockquote',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_ChildDef_StrictBlockquote',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_ChildDef_Table' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_ChildDef_Table',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_ChildDef_Table',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Config' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Config',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Config',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_ConfigSchema' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_ConfigSchema',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_ConfigSchema',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_ConfigSchema_Builder_ConfigSchema' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_ConfigSchema_Builder_ConfigSchema',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_ConfigSchema_Builder_ConfigSchema',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_ConfigSchema_Builder_Xml' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_ConfigSchema_Builder_Xml',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_ConfigSchema_Builder_Xml',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_ConfigSchema_Exception' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_ConfigSchema_Exception',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_ConfigSchema_Exception',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_ConfigSchema_Interchange' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_ConfigSchema_Interchange',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_ConfigSchema_Interchange',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_ConfigSchema_Interchange_Directive' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_ConfigSchema_Interchange_Directive',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_ConfigSchema_Interchange_Directive',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_ConfigSchema_Interchange_Id' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_ConfigSchema_Interchange_Id',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_ConfigSchema_Interchange_Id',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_ConfigSchema_InterchangeBuilder' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_ConfigSchema_InterchangeBuilder',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_ConfigSchema_InterchangeBuilder',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_ConfigSchema_Validator' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_ConfigSchema_Validator',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_ConfigSchema_Validator',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_ConfigSchema_ValidatorAtom' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_ConfigSchema_ValidatorAtom',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_ConfigSchema_ValidatorAtom',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_ContentSets' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_ContentSets',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_ContentSets',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Context' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Context',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Context',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Definition' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Definition',
    'isabstract' => true,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Definition',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_DefinitionCache' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_DefinitionCache',
    'isabstract' => true,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_DefinitionCache',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_DefinitionCache_Decorator' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_DefinitionCache_Decorator',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_DefinitionCache_Decorator',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_DefinitionCache_Decorator_Cleanup' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_DefinitionCache_Decorator_Cleanup',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_DefinitionCache_Decorator_Cleanup',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_DefinitionCache_Decorator_Memory' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_DefinitionCache_Decorator_Memory',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_DefinitionCache_Decorator_Memory',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_DefinitionCache_Null' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_DefinitionCache_Null',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_DefinitionCache_Null',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_DefinitionCache_Serializer' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_DefinitionCache_Serializer',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_DefinitionCache_Serializer',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_DefinitionCacheFactory' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_DefinitionCacheFactory',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_DefinitionCacheFactory',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Doctype' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Doctype',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Doctype',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_DoctypeRegistry' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_DoctypeRegistry',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_DoctypeRegistry',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_ElementDef' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_ElementDef',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_ElementDef',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Encoder' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Encoder',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Encoder',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_EntityLookup' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_EntityLookup',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_EntityLookup',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_EntityParser' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_EntityParser',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_EntityParser',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_ErrorCollector' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_ErrorCollector',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_ErrorCollector',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_ErrorStruct' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_ErrorStruct',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_ErrorStruct',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Exception' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Exception',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Exception',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Filter' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Filter',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Filter',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Filter_ExtractStyleBlocks' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Filter_ExtractStyleBlocks',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Filter_ExtractStyleBlocks',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Filter_YouTube' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Filter_YouTube',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Filter_YouTube',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Generator' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Generator',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Generator',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_HTMLDefinition' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_HTMLDefinition',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_HTMLDefinition',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_HTMLModule' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_HTMLModule',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_HTMLModule',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_HTMLModule_Bdo' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_HTMLModule_Bdo',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_HTMLModule_Bdo',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_HTMLModule_CommonAttributes' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_HTMLModule_CommonAttributes',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_HTMLModule_CommonAttributes',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_HTMLModule_Edit' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_HTMLModule_Edit',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_HTMLModule_Edit',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_HTMLModule_Forms' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_HTMLModule_Forms',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_HTMLModule_Forms',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_HTMLModule_Hypertext' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_HTMLModule_Hypertext',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_HTMLModule_Hypertext',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_HTMLModule_Iframe' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_HTMLModule_Iframe',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_HTMLModule_Iframe',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_HTMLModule_Image' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_HTMLModule_Image',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_HTMLModule_Image',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_HTMLModule_Legacy' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_HTMLModule_Legacy',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_HTMLModule_Legacy',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_HTMLModule_List' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_HTMLModule_List',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_HTMLModule_List',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_HTMLModule_Name' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_HTMLModule_Name',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_HTMLModule_Name',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_HTMLModule_Nofollow' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_HTMLModule_Nofollow',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_HTMLModule_Nofollow',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_HTMLModule_NonXMLCommonAttributes' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_HTMLModule_NonXMLCommonAttributes',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_HTMLModule_NonXMLCommonAttributes',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_HTMLModule_Object' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_HTMLModule_Object',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_HTMLModule_Object',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_HTMLModule_Presentation' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_HTMLModule_Presentation',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_HTMLModule_Presentation',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_HTMLModule_Proprietary' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_HTMLModule_Proprietary',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_HTMLModule_Proprietary',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_HTMLModule_Ruby' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_HTMLModule_Ruby',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_HTMLModule_Ruby',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_HTMLModule_SafeEmbed' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_HTMLModule_SafeEmbed',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_HTMLModule_SafeEmbed',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_HTMLModule_SafeObject' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_HTMLModule_SafeObject',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_HTMLModule_SafeObject',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_HTMLModule_SafeScripting' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_HTMLModule_SafeScripting',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_HTMLModule_SafeScripting',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_HTMLModule_Scripting' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_HTMLModule_Scripting',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_HTMLModule_Scripting',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_HTMLModule_StyleAttribute' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_HTMLModule_StyleAttribute',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_HTMLModule_StyleAttribute',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_HTMLModule_Tables' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_HTMLModule_Tables',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_HTMLModule_Tables',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_HTMLModule_Target' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_HTMLModule_Target',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_HTMLModule_Target',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_HTMLModule_TargetBlank' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_HTMLModule_TargetBlank',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_HTMLModule_TargetBlank',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_HTMLModule_TargetNoopener' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_HTMLModule_TargetNoopener',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_HTMLModule_TargetNoopener',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_HTMLModule_TargetNoreferrer' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_HTMLModule_TargetNoreferrer',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_HTMLModule_TargetNoreferrer',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_HTMLModule_Text' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_HTMLModule_Text',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_HTMLModule_Text',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_HTMLModule_Tidy' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_HTMLModule_Tidy',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_HTMLModule_Tidy',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_HTMLModule_Tidy_Name' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_HTMLModule_Tidy_Name',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_HTMLModule_Tidy_Name',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_HTMLModule_Tidy_Proprietary' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_HTMLModule_Tidy_Proprietary',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_HTMLModule_Tidy_Proprietary',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_HTMLModule_Tidy_Strict' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_HTMLModule_Tidy_Strict',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_HTMLModule_Tidy_Strict',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_HTMLModule_Tidy_Transitional' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_HTMLModule_Tidy_Transitional',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_HTMLModule_Tidy_Transitional',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_HTMLModule_Tidy_XHTML' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_HTMLModule_Tidy_XHTML',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_HTMLModule_Tidy_XHTML',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_HTMLModule_Tidy_XHTMLAndHTML4' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_HTMLModule_Tidy_XHTMLAndHTML4',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_HTMLModule_Tidy_XHTMLAndHTML4',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_HTMLModule_XMLCommonAttributes' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_HTMLModule_XMLCommonAttributes',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_HTMLModule_XMLCommonAttributes',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_HTMLModuleManager' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_HTMLModuleManager',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_HTMLModuleManager',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_IDAccumulator' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_IDAccumulator',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_IDAccumulator',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Injector' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Injector',
    'isabstract' => true,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Injector',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Injector_AutoParagraph' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Injector_AutoParagraph',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Injector_AutoParagraph',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Injector_DisplayLinkURI' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Injector_DisplayLinkURI',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Injector_DisplayLinkURI',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Injector_Linkify' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Injector_Linkify',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Injector_Linkify',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Injector_PurifierLinkify' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Injector_PurifierLinkify',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Injector_PurifierLinkify',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Injector_RemoveEmpty' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Injector_RemoveEmpty',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Injector_RemoveEmpty',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Injector_RemoveSpansWithoutAttributes' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Injector_RemoveSpansWithoutAttributes',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Injector_RemoveSpansWithoutAttributes',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Injector_SafeObject' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Injector_SafeObject',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Injector_SafeObject',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Language' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Language',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Language',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_LanguageFactory' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_LanguageFactory',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_LanguageFactory',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Length' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Length',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Length',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Lexer' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Lexer',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Lexer',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Lexer_DOMLex' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Lexer_DOMLex',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Lexer_DOMLex',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Lexer_DirectLex' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Lexer_DirectLex',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Lexer_DirectLex',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Lexer_PH5P' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Lexer_PH5P',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Lexer_PH5P',
    'implements' => 
    array (
    ),
  ),
  'HTML5' => 
  array (
    'type' => 'class',
    'classname' => 'HTML5',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTML5',
    'implements' => 
    array (
    ),
  ),
  'HTML5TreeConstructer' => 
  array (
    'type' => 'class',
    'classname' => 'HTML5TreeConstructer',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTML5TreeConstructer',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Node' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Node',
    'isabstract' => true,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Node',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Node_Comment' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Node_Comment',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Node_Comment',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Node_Element' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Node_Element',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Node_Element',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Node_Text' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Node_Text',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Node_Text',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_PercentEncoder' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_PercentEncoder',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_PercentEncoder',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Printer' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Printer',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Printer',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Printer_CSSDefinition' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Printer_CSSDefinition',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Printer_CSSDefinition',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Printer_ConfigForm' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Printer_ConfigForm',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Printer_ConfigForm',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Printer_ConfigForm_NullDecorator' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Printer_ConfigForm_NullDecorator',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Printer_ConfigForm_NullDecorator',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Printer_ConfigForm_default' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Printer_ConfigForm_default',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Printer_ConfigForm_default',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Printer_ConfigForm_bool' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Printer_ConfigForm_bool',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Printer_ConfigForm_bool',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Printer_HTMLDefinition' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Printer_HTMLDefinition',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Printer_HTMLDefinition',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_PropertyList' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_PropertyList',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_PropertyList',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_PropertyListIterator' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_PropertyListIterator',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_PropertyListIterator',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Queue' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Queue',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Queue',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Strategy' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Strategy',
    'isabstract' => true,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Strategy',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Strategy_Composite' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Strategy_Composite',
    'isabstract' => true,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Strategy_Composite',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Strategy_Core' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Strategy_Core',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Strategy_Core',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Strategy_FixNesting' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Strategy_FixNesting',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Strategy_FixNesting',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Strategy_MakeWellFormed' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Strategy_MakeWellFormed',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Strategy_MakeWellFormed',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Strategy_RemoveForeignElements' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Strategy_RemoveForeignElements',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Strategy_RemoveForeignElements',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Strategy_ValidateAttributes' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Strategy_ValidateAttributes',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Strategy_ValidateAttributes',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_StringHash' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_StringHash',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_StringHash',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_StringHashParser' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_StringHashParser',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_StringHashParser',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_TagTransform' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_TagTransform',
    'isabstract' => true,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_TagTransform',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_TagTransform_Font' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_TagTransform_Font',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_TagTransform_Font',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_TagTransform_Simple' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_TagTransform_Simple',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_TagTransform_Simple',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Token' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Token',
    'isabstract' => true,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Token',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Token_Comment' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Token_Comment',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Token_Comment',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Token_Empty' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Token_Empty',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Token_Empty',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Token_End' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Token_End',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Token_End',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Token_Start' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Token_Start',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Token_Start',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Token_Tag' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Token_Tag',
    'isabstract' => true,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Token_Tag',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Token_Text' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Token_Text',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Token_Text',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_TokenFactory' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_TokenFactory',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_TokenFactory',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_URI' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_URI',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_URI',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_URIDefinition' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_URIDefinition',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_URIDefinition',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_URIFilter' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_URIFilter',
    'isabstract' => true,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_URIFilter',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_URIFilter_DisableExternal' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_URIFilter_DisableExternal',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_URIFilter_DisableExternal',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_URIFilter_DisableExternalResources' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_URIFilter_DisableExternalResources',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_URIFilter_DisableExternalResources',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_URIFilter_DisableResources' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_URIFilter_DisableResources',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_URIFilter_DisableResources',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_URIFilter_HostBlacklist' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_URIFilter_HostBlacklist',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_URIFilter_HostBlacklist',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_URIFilter_MakeAbsolute' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_URIFilter_MakeAbsolute',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_URIFilter_MakeAbsolute',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_URIFilter_Munge' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_URIFilter_Munge',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_URIFilter_Munge',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_URIFilter_SafeIframe' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_URIFilter_SafeIframe',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_URIFilter_SafeIframe',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_URIParser' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_URIParser',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_URIParser',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_URIScheme' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_URIScheme',
    'isabstract' => true,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_URIScheme',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_URIScheme_data' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_URIScheme_data',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_URIScheme_data',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_URIScheme_file' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_URIScheme_file',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_URIScheme_file',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_URIScheme_ftp' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_URIScheme_ftp',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_URIScheme_ftp',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_URIScheme_http' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_URIScheme_http',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_URIScheme_http',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_URIScheme_https' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_URIScheme_https',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_URIScheme_https',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_URIScheme_mailto' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_URIScheme_mailto',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_URIScheme_mailto',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_URIScheme_news' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_URIScheme_news',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_URIScheme_news',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_URIScheme_nntp' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_URIScheme_nntp',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_URIScheme_nntp',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_URIScheme_tel' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_URIScheme_tel',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_URIScheme_tel',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_URISchemeRegistry' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_URISchemeRegistry',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_URISchemeRegistry',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_UnitConverter' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_UnitConverter',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_UnitConverter',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_VarParser' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_VarParser',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_VarParser',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_VarParser_Flexible' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_VarParser_Flexible',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_VarParser_Flexible',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_VarParser_Native' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_VarParser_Native',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_VarParser_Native',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_VarParserException' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_VarParserException',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_VarParserException',
    'implements' => 
    array (
    ),
  ),
  'HTMLPurifier_Zipper' => 
  array (
    'type' => 'class',
    'classname' => 'HTMLPurifier_Zipper',
    'isabstract' => false,
    'namespace' => '\\',
    'extends' => 'LearnDash_Reports_HTMLPurifier_Zipper',
    'implements' => 
    array (
    ),
  ),
  'ZipStream\\Bigint' => 
  array (
    'type' => 'class',
    'classname' => 'Bigint',
    'isabstract' => false,
    'namespace' => 'ZipStream',
    'extends' => 'LearnDash\\Reports\\ZipStream\\Bigint',
    'implements' => 
    array (
    ),
  ),
  'ZipStream\\DeflateStream' => 
  array (
    'type' => 'class',
    'classname' => 'DeflateStream',
    'isabstract' => false,
    'namespace' => 'ZipStream',
    'extends' => 'LearnDash\\Reports\\ZipStream\\DeflateStream',
    'implements' => 
    array (
    ),
  ),
  'ZipStream\\Exception\\EncodingException' => 
  array (
    'type' => 'class',
    'classname' => 'EncodingException',
    'isabstract' => false,
    'namespace' => 'ZipStream\\Exception',
    'extends' => 'LearnDash\\Reports\\ZipStream\\Exception\\EncodingException',
    'implements' => 
    array (
    ),
  ),
  'ZipStream\\Exception\\FileNotFoundException' => 
  array (
    'type' => 'class',
    'classname' => 'FileNotFoundException',
    'isabstract' => false,
    'namespace' => 'ZipStream\\Exception',
    'extends' => 'LearnDash\\Reports\\ZipStream\\Exception\\FileNotFoundException',
    'implements' => 
    array (
    ),
  ),
  'ZipStream\\Exception\\FileNotReadableException' => 
  array (
    'type' => 'class',
    'classname' => 'FileNotReadableException',
    'isabstract' => false,
    'namespace' => 'ZipStream\\Exception',
    'extends' => 'LearnDash\\Reports\\ZipStream\\Exception\\FileNotReadableException',
    'implements' => 
    array (
    ),
  ),
  'ZipStream\\Exception\\IncompatibleOptionsException' => 
  array (
    'type' => 'class',
    'classname' => 'IncompatibleOptionsException',
    'isabstract' => false,
    'namespace' => 'ZipStream\\Exception',
    'extends' => 'LearnDash\\Reports\\ZipStream\\Exception\\IncompatibleOptionsException',
    'implements' => 
    array (
    ),
  ),
  'ZipStream\\Exception\\OverflowException' => 
  array (
    'type' => 'class',
    'classname' => 'OverflowException',
    'isabstract' => false,
    'namespace' => 'ZipStream\\Exception',
    'extends' => 'LearnDash\\Reports\\ZipStream\\Exception\\OverflowException',
    'implements' => 
    array (
    ),
  ),
  'ZipStream\\Exception\\StreamNotReadableException' => 
  array (
    'type' => 'class',
    'classname' => 'StreamNotReadableException',
    'isabstract' => false,
    'namespace' => 'ZipStream\\Exception',
    'extends' => 'LearnDash\\Reports\\ZipStream\\Exception\\StreamNotReadableException',
    'implements' => 
    array (
    ),
  ),
  'ZipStream\\File' => 
  array (
    'type' => 'class',
    'classname' => 'File',
    'isabstract' => false,
    'namespace' => 'ZipStream',
    'extends' => 'LearnDash\\Reports\\ZipStream\\File',
    'implements' => 
    array (
    ),
  ),
  'ZipStream\\Option\\Archive' => 
  array (
    'type' => 'class',
    'classname' => 'Archive',
    'isabstract' => false,
    'namespace' => 'ZipStream\\Option',
    'extends' => 'LearnDash\\Reports\\ZipStream\\Option\\Archive',
    'implements' => 
    array (
    ),
  ),
  'ZipStream\\Option\\File' => 
  array (
    'type' => 'class',
    'classname' => 'File',
    'isabstract' => false,
    'namespace' => 'ZipStream\\Option',
    'extends' => 'LearnDash\\Reports\\ZipStream\\Option\\File',
    'implements' => 
    array (
    ),
  ),
  'ZipStream\\Option\\Method' => 
  array (
    'type' => 'class',
    'classname' => 'Method',
    'isabstract' => false,
    'namespace' => 'ZipStream\\Option',
    'extends' => 'LearnDash\\Reports\\ZipStream\\Option\\Method',
    'implements' => 
    array (
    ),
  ),
  'ZipStream\\Option\\Version' => 
  array (
    'type' => 'class',
    'classname' => 'Version',
    'isabstract' => false,
    'namespace' => 'ZipStream\\Option',
    'extends' => 'LearnDash\\Reports\\ZipStream\\Option\\Version',
    'implements' => 
    array (
    ),
  ),
  'ZipStream\\Stream' => 
  array (
    'type' => 'class',
    'classname' => 'Stream',
    'isabstract' => false,
    'namespace' => 'ZipStream',
    'extends' => 'LearnDash\\Reports\\ZipStream\\Stream',
    'implements' => 
    array (
      0 => 'Psr\\Http\\Message\\StreamInterface',
    ),
  ),
  'ZipStream\\ZipStream' => 
  array (
    'type' => 'class',
    'classname' => 'ZipStream',
    'isabstract' => false,
    'namespace' => 'ZipStream',
    'extends' => 'LearnDash\\Reports\\ZipStream\\ZipStream',
    'implements' => 
    array (
    ),
  ),
  'BigintTest\\BigintTest' => 
  array (
    'type' => 'class',
    'classname' => 'BigintTest',
    'isabstract' => false,
    'namespace' => 'BigintTest',
    'extends' => 'LearnDash\\Reports\\BigintTest\\BigintTest',
    'implements' => 
    array (
    ),
  ),
  'ZipStreamTest\\ZipStreamTest' => 
  array (
    'type' => 'class',
    'classname' => 'ZipStreamTest',
    'isabstract' => false,
    'namespace' => 'ZipStreamTest',
    'extends' => 'LearnDash\\Reports\\ZipStreamTest\\ZipStreamTest',
    'implements' => 
    array (
    ),
  ),
  'BugHonorFileTimeTest\\BugHonorFileTimeTest' => 
  array (
    'type' => 'class',
    'classname' => 'BugHonorFileTimeTest',
    'isabstract' => false,
    'namespace' => 'BugHonorFileTimeTest',
    'extends' => 'LearnDash\\Reports\\BugHonorFileTimeTest\\BugHonorFileTimeTest',
    'implements' => 
    array (
    ),
  ),
  'Complex\\Complex' => 
  array (
    'type' => 'class',
    'classname' => 'Complex',
    'isabstract' => false,
    'namespace' => 'Complex',
    'extends' => 'LearnDash\\Reports\\Complex\\Complex',
    'implements' => 
    array (
    ),
  ),
  'Complex\\Exception' => 
  array (
    'type' => 'class',
    'classname' => 'Exception',
    'isabstract' => false,
    'namespace' => 'Complex',
    'extends' => 'LearnDash\\Reports\\Complex\\Exception',
    'implements' => 
    array (
    ),
  ),
  'Complex\\Functions' => 
  array (
    'type' => 'class',
    'classname' => 'Functions',
    'isabstract' => false,
    'namespace' => 'Complex',
    'extends' => 'LearnDash\\Reports\\Complex\\Functions',
    'implements' => 
    array (
    ),
  ),
  'Complex\\Operations' => 
  array (
    'type' => 'class',
    'classname' => 'Operations',
    'isabstract' => false,
    'namespace' => 'Complex',
    'extends' => 'LearnDash\\Reports\\Complex\\Operations',
    'implements' => 
    array (
    ),
  ),
  'Matrix\\Builder' => 
  array (
    'type' => 'class',
    'classname' => 'Builder',
    'isabstract' => false,
    'namespace' => 'Matrix',
    'extends' => 'LearnDash\\Reports\\Matrix\\Builder',
    'implements' => 
    array (
    ),
  ),
  'Matrix\\Decomposition\\Decomposition' => 
  array (
    'type' => 'class',
    'classname' => 'Decomposition',
    'isabstract' => false,
    'namespace' => 'Matrix\\Decomposition',
    'extends' => 'LearnDash\\Reports\\Matrix\\Decomposition\\Decomposition',
    'implements' => 
    array (
    ),
  ),
  'Matrix\\Decomposition\\LU' => 
  array (
    'type' => 'class',
    'classname' => 'LU',
    'isabstract' => false,
    'namespace' => 'Matrix\\Decomposition',
    'extends' => 'LearnDash\\Reports\\Matrix\\Decomposition\\LU',
    'implements' => 
    array (
    ),
  ),
  'Matrix\\Decomposition\\QR' => 
  array (
    'type' => 'class',
    'classname' => 'QR',
    'isabstract' => false,
    'namespace' => 'Matrix\\Decomposition',
    'extends' => 'LearnDash\\Reports\\Matrix\\Decomposition\\QR',
    'implements' => 
    array (
    ),
  ),
  'Matrix\\Div0Exception' => 
  array (
    'type' => 'class',
    'classname' => 'Div0Exception',
    'isabstract' => false,
    'namespace' => 'Matrix',
    'extends' => 'LearnDash\\Reports\\Matrix\\Div0Exception',
    'implements' => 
    array (
    ),
  ),
  'Matrix\\Exception' => 
  array (
    'type' => 'class',
    'classname' => 'Exception',
    'isabstract' => false,
    'namespace' => 'Matrix',
    'extends' => 'LearnDash\\Reports\\Matrix\\Exception',
    'implements' => 
    array (
    ),
  ),
  'Matrix\\Functions' => 
  array (
    'type' => 'class',
    'classname' => 'Functions',
    'isabstract' => false,
    'namespace' => 'Matrix',
    'extends' => 'LearnDash\\Reports\\Matrix\\Functions',
    'implements' => 
    array (
    ),
  ),
  'Matrix\\Matrix' => 
  array (
    'type' => 'class',
    'classname' => 'Matrix',
    'isabstract' => false,
    'namespace' => 'Matrix',
    'extends' => 'LearnDash\\Reports\\Matrix\\Matrix',
    'implements' => 
    array (
    ),
  ),
  'Matrix\\Operations' => 
  array (
    'type' => 'class',
    'classname' => 'Operations',
    'isabstract' => false,
    'namespace' => 'Matrix',
    'extends' => 'LearnDash\\Reports\\Matrix\\Operations',
    'implements' => 
    array (
    ),
  ),
  'Matrix\\Operators\\Addition' => 
  array (
    'type' => 'class',
    'classname' => 'Addition',
    'isabstract' => false,
    'namespace' => 'Matrix\\Operators',
    'extends' => 'LearnDash\\Reports\\Matrix\\Operators\\Addition',
    'implements' => 
    array (
    ),
  ),
  'Matrix\\Operators\\DirectSum' => 
  array (
    'type' => 'class',
    'classname' => 'DirectSum',
    'isabstract' => false,
    'namespace' => 'Matrix\\Operators',
    'extends' => 'LearnDash\\Reports\\Matrix\\Operators\\DirectSum',
    'implements' => 
    array (
    ),
  ),
  'Matrix\\Operators\\Division' => 
  array (
    'type' => 'class',
    'classname' => 'Division',
    'isabstract' => false,
    'namespace' => 'Matrix\\Operators',
    'extends' => 'LearnDash\\Reports\\Matrix\\Operators\\Division',
    'implements' => 
    array (
    ),
  ),
  'Matrix\\Operators\\Multiplication' => 
  array (
    'type' => 'class',
    'classname' => 'Multiplication',
    'isabstract' => false,
    'namespace' => 'Matrix\\Operators',
    'extends' => 'LearnDash\\Reports\\Matrix\\Operators\\Multiplication',
    'implements' => 
    array (
    ),
  ),
  'Matrix\\Operators\\Operator' => 
  array (
    'type' => 'class',
    'classname' => 'Operator',
    'isabstract' => true,
    'namespace' => 'Matrix\\Operators',
    'extends' => 'LearnDash\\Reports\\Matrix\\Operators\\Operator',
    'implements' => 
    array (
    ),
  ),
  'Matrix\\Operators\\Subtraction' => 
  array (
    'type' => 'class',
    'classname' => 'Subtraction',
    'isabstract' => false,
    'namespace' => 'Matrix\\Operators',
    'extends' => 'LearnDash\\Reports\\Matrix\\Operators\\Subtraction',
    'implements' => 
    array (
    ),
  ),
  'MyCLabs\\Enum\\Enum' => 
  array (
    'type' => 'class',
    'classname' => 'Enum',
    'isabstract' => true,
    'namespace' => 'MyCLabs\\Enum',
    'extends' => 'LearnDash\\Reports\\MyCLabs\\Enum\\Enum',
    'implements' => 
    array (
      0 => 'JsonSerializable',
      1 => 'Stringable',
    ),
  ),
  'MyCLabs\\Enum\\PHPUnit\\Comparator' => 
  array (
    'type' => 'class',
    'classname' => 'Comparator',
    'isabstract' => false,
    'namespace' => 'MyCLabs\\Enum\\PHPUnit',
    'extends' => 'LearnDash\\Reports\\MyCLabs\\Enum\\PHPUnit\\Comparator',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\BinaryComparison' => 
  array (
    'type' => 'class',
    'classname' => 'BinaryComparison',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\BinaryComparison',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Calculation' => 
  array (
    'type' => 'class',
    'classname' => 'Calculation',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Calculation',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Category' => 
  array (
    'type' => 'class',
    'classname' => 'Category',
    'isabstract' => true,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Category',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Database\\DAverage' => 
  array (
    'type' => 'class',
    'classname' => 'DAverage',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Database',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Database\\DAverage',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Database\\DCount' => 
  array (
    'type' => 'class',
    'classname' => 'DCount',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Database',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Database\\DCount',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Database\\DCountA' => 
  array (
    'type' => 'class',
    'classname' => 'DCountA',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Database',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Database\\DCountA',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Database\\DGet' => 
  array (
    'type' => 'class',
    'classname' => 'DGet',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Database',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Database\\DGet',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Database\\DMax' => 
  array (
    'type' => 'class',
    'classname' => 'DMax',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Database',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Database\\DMax',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Database\\DMin' => 
  array (
    'type' => 'class',
    'classname' => 'DMin',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Database',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Database\\DMin',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Database\\DProduct' => 
  array (
    'type' => 'class',
    'classname' => 'DProduct',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Database',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Database\\DProduct',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Database\\DStDev' => 
  array (
    'type' => 'class',
    'classname' => 'DStDev',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Database',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Database\\DStDev',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Database\\DStDevP' => 
  array (
    'type' => 'class',
    'classname' => 'DStDevP',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Database',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Database\\DStDevP',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Database\\DSum' => 
  array (
    'type' => 'class',
    'classname' => 'DSum',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Database',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Database\\DSum',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Database\\DVar' => 
  array (
    'type' => 'class',
    'classname' => 'DVar',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Database',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Database\\DVar',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Database\\DVarP' => 
  array (
    'type' => 'class',
    'classname' => 'DVarP',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Database',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Database\\DVarP',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Database\\DatabaseAbstract' => 
  array (
    'type' => 'class',
    'classname' => 'DatabaseAbstract',
    'isabstract' => true,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Database',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Database\\DatabaseAbstract',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\DateTime' => 
  array (
    'type' => 'class',
    'classname' => 'DateTime',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\DateTime',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel\\Constants' => 
  array (
    'type' => 'class',
    'classname' => 'Constants',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel\\Constants',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel\\Current' => 
  array (
    'type' => 'class',
    'classname' => 'Current',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel\\Current',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel\\Date' => 
  array (
    'type' => 'class',
    'classname' => 'Date',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel\\Date',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel\\DateParts' => 
  array (
    'type' => 'class',
    'classname' => 'DateParts',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel\\DateParts',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel\\DateValue' => 
  array (
    'type' => 'class',
    'classname' => 'DateValue',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel\\DateValue',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel\\Days' => 
  array (
    'type' => 'class',
    'classname' => 'Days',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel\\Days',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel\\Days360' => 
  array (
    'type' => 'class',
    'classname' => 'Days360',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel\\Days360',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel\\Difference' => 
  array (
    'type' => 'class',
    'classname' => 'Difference',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel\\Difference',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel\\Helpers' => 
  array (
    'type' => 'class',
    'classname' => 'Helpers',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel\\Helpers',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel\\Month' => 
  array (
    'type' => 'class',
    'classname' => 'Month',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel\\Month',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel\\NetworkDays' => 
  array (
    'type' => 'class',
    'classname' => 'NetworkDays',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel\\NetworkDays',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel\\Time' => 
  array (
    'type' => 'class',
    'classname' => 'Time',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel\\Time',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel\\TimeParts' => 
  array (
    'type' => 'class',
    'classname' => 'TimeParts',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel\\TimeParts',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel\\TimeValue' => 
  array (
    'type' => 'class',
    'classname' => 'TimeValue',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel\\TimeValue',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel\\Week' => 
  array (
    'type' => 'class',
    'classname' => 'Week',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel\\Week',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel\\WorkDay' => 
  array (
    'type' => 'class',
    'classname' => 'WorkDay',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel\\WorkDay',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel\\YearFrac' => 
  array (
    'type' => 'class',
    'classname' => 'YearFrac',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\DateTimeExcel\\YearFrac',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Engine\\ArrayArgumentHelper' => 
  array (
    'type' => 'class',
    'classname' => 'ArrayArgumentHelper',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Engine',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Engine\\ArrayArgumentHelper',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Engine\\ArrayArgumentProcessor' => 
  array (
    'type' => 'class',
    'classname' => 'ArrayArgumentProcessor',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Engine',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Engine\\ArrayArgumentProcessor',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Engine\\BranchPruner' => 
  array (
    'type' => 'class',
    'classname' => 'BranchPruner',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Engine',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Engine\\BranchPruner',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Engine\\CyclicReferenceStack' => 
  array (
    'type' => 'class',
    'classname' => 'CyclicReferenceStack',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Engine',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Engine\\CyclicReferenceStack',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Engine\\FormattedNumber' => 
  array (
    'type' => 'class',
    'classname' => 'FormattedNumber',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Engine',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Engine\\FormattedNumber',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Engine\\Logger' => 
  array (
    'type' => 'class',
    'classname' => 'Logger',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Engine',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Engine\\Logger',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Engine\\Operands\\StructuredReference' => 
  array (
    'type' => 'class',
    'classname' => 'StructuredReference',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Engine\\Operands',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Engine\\Operands\\StructuredReference',
    'implements' => 
    array (
      0 => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Engine\\Operands\\Operand',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering\\BesselI' => 
  array (
    'type' => 'class',
    'classname' => 'BesselI',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering\\BesselI',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering\\BesselJ' => 
  array (
    'type' => 'class',
    'classname' => 'BesselJ',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering\\BesselJ',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering\\BesselK' => 
  array (
    'type' => 'class',
    'classname' => 'BesselK',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering\\BesselK',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering\\BesselY' => 
  array (
    'type' => 'class',
    'classname' => 'BesselY',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering\\BesselY',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering\\BitWise' => 
  array (
    'type' => 'class',
    'classname' => 'BitWise',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering\\BitWise',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering\\Compare' => 
  array (
    'type' => 'class',
    'classname' => 'Compare',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering\\Compare',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering\\Complex' => 
  array (
    'type' => 'class',
    'classname' => 'Complex',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering\\Complex',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering\\ComplexFunctions' => 
  array (
    'type' => 'class',
    'classname' => 'ComplexFunctions',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering\\ComplexFunctions',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering\\ComplexOperations' => 
  array (
    'type' => 'class',
    'classname' => 'ComplexOperations',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering\\ComplexOperations',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering\\Constants' => 
  array (
    'type' => 'class',
    'classname' => 'Constants',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering\\Constants',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering\\ConvertBase' => 
  array (
    'type' => 'class',
    'classname' => 'ConvertBase',
    'isabstract' => true,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering\\ConvertBase',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering\\ConvertBinary' => 
  array (
    'type' => 'class',
    'classname' => 'ConvertBinary',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering\\ConvertBinary',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering\\ConvertDecimal' => 
  array (
    'type' => 'class',
    'classname' => 'ConvertDecimal',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering\\ConvertDecimal',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering\\ConvertHex' => 
  array (
    'type' => 'class',
    'classname' => 'ConvertHex',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering\\ConvertHex',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering\\ConvertOctal' => 
  array (
    'type' => 'class',
    'classname' => 'ConvertOctal',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering\\ConvertOctal',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering\\ConvertUOM' => 
  array (
    'type' => 'class',
    'classname' => 'ConvertUOM',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering\\ConvertUOM',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering\\EngineeringValidations' => 
  array (
    'type' => 'class',
    'classname' => 'EngineeringValidations',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering\\EngineeringValidations',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering\\Erf' => 
  array (
    'type' => 'class',
    'classname' => 'Erf',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering\\Erf',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering\\ErfC' => 
  array (
    'type' => 'class',
    'classname' => 'ErfC',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Engineering\\ErfC',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Exception' => 
  array (
    'type' => 'class',
    'classname' => 'Exception',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Exception',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\ExceptionHandler' => 
  array (
    'type' => 'class',
    'classname' => 'ExceptionHandler',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\ExceptionHandler',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\Amortization' => 
  array (
    'type' => 'class',
    'classname' => 'Amortization',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\Amortization',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\CashFlow\\CashFlowValidations' => 
  array (
    'type' => 'class',
    'classname' => 'CashFlowValidations',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\CashFlow',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\CashFlow\\CashFlowValidations',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\CashFlow\\Constant\\Periodic\\Cumulative' => 
  array (
    'type' => 'class',
    'classname' => 'Cumulative',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\CashFlow\\Constant\\Periodic',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\CashFlow\\Constant\\Periodic\\Cumulative',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\CashFlow\\Constant\\Periodic\\Interest' => 
  array (
    'type' => 'class',
    'classname' => 'Interest',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\CashFlow\\Constant\\Periodic',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\CashFlow\\Constant\\Periodic\\Interest',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\CashFlow\\Constant\\Periodic\\InterestAndPrincipal' => 
  array (
    'type' => 'class',
    'classname' => 'InterestAndPrincipal',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\CashFlow\\Constant\\Periodic',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\CashFlow\\Constant\\Periodic\\InterestAndPrincipal',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\CashFlow\\Constant\\Periodic\\Payments' => 
  array (
    'type' => 'class',
    'classname' => 'Payments',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\CashFlow\\Constant\\Periodic',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\CashFlow\\Constant\\Periodic\\Payments',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\CashFlow\\Single' => 
  array (
    'type' => 'class',
    'classname' => 'Single',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\CashFlow',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\CashFlow\\Single',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\CashFlow\\Variable\\NonPeriodic' => 
  array (
    'type' => 'class',
    'classname' => 'NonPeriodic',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\CashFlow\\Variable',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\CashFlow\\Variable\\NonPeriodic',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\CashFlow\\Variable\\Periodic' => 
  array (
    'type' => 'class',
    'classname' => 'Periodic',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\CashFlow\\Variable',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\CashFlow\\Variable\\Periodic',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\Constants' => 
  array (
    'type' => 'class',
    'classname' => 'Constants',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\Constants',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\Coupons' => 
  array (
    'type' => 'class',
    'classname' => 'Coupons',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\Coupons',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\Depreciation' => 
  array (
    'type' => 'class',
    'classname' => 'Depreciation',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\Depreciation',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\Dollar' => 
  array (
    'type' => 'class',
    'classname' => 'Dollar',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\Dollar',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\FinancialValidations' => 
  array (
    'type' => 'class',
    'classname' => 'FinancialValidations',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\FinancialValidations',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\Helpers' => 
  array (
    'type' => 'class',
    'classname' => 'Helpers',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\Helpers',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\InterestRate' => 
  array (
    'type' => 'class',
    'classname' => 'InterestRate',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\InterestRate',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\Securities\\AccruedInterest' => 
  array (
    'type' => 'class',
    'classname' => 'AccruedInterest',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\Securities',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\Securities\\AccruedInterest',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\Securities\\Price' => 
  array (
    'type' => 'class',
    'classname' => 'Price',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\Securities',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\Securities\\Price',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\Securities\\Rates' => 
  array (
    'type' => 'class',
    'classname' => 'Rates',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\Securities',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\Securities\\Rates',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\Securities\\SecurityValidations' => 
  array (
    'type' => 'class',
    'classname' => 'SecurityValidations',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\Securities',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\Securities\\SecurityValidations',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\Securities\\Yields' => 
  array (
    'type' => 'class',
    'classname' => 'Yields',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\Securities',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\Securities\\Yields',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\TreasuryBill' => 
  array (
    'type' => 'class',
    'classname' => 'TreasuryBill',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Financial',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Financial\\TreasuryBill',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\FormulaParser' => 
  array (
    'type' => 'class',
    'classname' => 'FormulaParser',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\FormulaParser',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\FormulaToken' => 
  array (
    'type' => 'class',
    'classname' => 'FormulaToken',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\FormulaToken',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Functions' => 
  array (
    'type' => 'class',
    'classname' => 'Functions',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Functions',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Information\\ErrorValue' => 
  array (
    'type' => 'class',
    'classname' => 'ErrorValue',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Information',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Information\\ErrorValue',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Information\\ExcelError' => 
  array (
    'type' => 'class',
    'classname' => 'ExcelError',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Information',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Information\\ExcelError',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Information\\Value' => 
  array (
    'type' => 'class',
    'classname' => 'Value',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Information',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Information\\Value',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Internal\\MakeMatrix' => 
  array (
    'type' => 'class',
    'classname' => 'MakeMatrix',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Internal',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Internal\\MakeMatrix',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Internal\\WildcardMatch' => 
  array (
    'type' => 'class',
    'classname' => 'WildcardMatch',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Internal',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Internal\\WildcardMatch',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Logical\\Boolean' => 
  array (
    'type' => 'class',
    'classname' => 'Boolean',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Logical',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Logical\\Boolean',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Logical\\Conditional' => 
  array (
    'type' => 'class',
    'classname' => 'Conditional',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Logical',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Logical\\Conditional',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Logical\\Operations' => 
  array (
    'type' => 'class',
    'classname' => 'Operations',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Logical',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Logical\\Operations',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef\\Address' => 
  array (
    'type' => 'class',
    'classname' => 'Address',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef\\Address',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef\\ExcelMatch' => 
  array (
    'type' => 'class',
    'classname' => 'ExcelMatch',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef\\ExcelMatch',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef\\Filter' => 
  array (
    'type' => 'class',
    'classname' => 'Filter',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef\\Filter',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef\\Formula' => 
  array (
    'type' => 'class',
    'classname' => 'Formula',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef\\Formula',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef\\HLookup' => 
  array (
    'type' => 'class',
    'classname' => 'HLookup',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef\\HLookup',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef\\Helpers' => 
  array (
    'type' => 'class',
    'classname' => 'Helpers',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef\\Helpers',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef\\Hyperlink' => 
  array (
    'type' => 'class',
    'classname' => 'Hyperlink',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef\\Hyperlink',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef\\Indirect' => 
  array (
    'type' => 'class',
    'classname' => 'Indirect',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef\\Indirect',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef\\Lookup' => 
  array (
    'type' => 'class',
    'classname' => 'Lookup',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef\\Lookup',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef\\LookupBase' => 
  array (
    'type' => 'class',
    'classname' => 'LookupBase',
    'isabstract' => true,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef\\LookupBase',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef\\LookupRefValidations' => 
  array (
    'type' => 'class',
    'classname' => 'LookupRefValidations',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef\\LookupRefValidations',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef\\Matrix' => 
  array (
    'type' => 'class',
    'classname' => 'Matrix',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef\\Matrix',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef\\Offset' => 
  array (
    'type' => 'class',
    'classname' => 'Offset',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef\\Offset',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef\\RowColumnInformation' => 
  array (
    'type' => 'class',
    'classname' => 'RowColumnInformation',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef\\RowColumnInformation',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef\\Selection' => 
  array (
    'type' => 'class',
    'classname' => 'Selection',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef\\Selection',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef\\Sort' => 
  array (
    'type' => 'class',
    'classname' => 'Sort',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef\\Sort',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef\\Unique' => 
  array (
    'type' => 'class',
    'classname' => 'Unique',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef\\Unique',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef\\VLookup' => 
  array (
    'type' => 'class',
    'classname' => 'VLookup',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\LookupRef\\VLookup',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Absolute' => 
  array (
    'type' => 'class',
    'classname' => 'Absolute',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Absolute',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Angle' => 
  array (
    'type' => 'class',
    'classname' => 'Angle',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Angle',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Arabic' => 
  array (
    'type' => 'class',
    'classname' => 'Arabic',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Arabic',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Base' => 
  array (
    'type' => 'class',
    'classname' => 'Base',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Base',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Ceiling' => 
  array (
    'type' => 'class',
    'classname' => 'Ceiling',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Ceiling',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Combinations' => 
  array (
    'type' => 'class',
    'classname' => 'Combinations',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Combinations',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Exp' => 
  array (
    'type' => 'class',
    'classname' => 'Exp',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Exp',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Factorial' => 
  array (
    'type' => 'class',
    'classname' => 'Factorial',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Factorial',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Floor' => 
  array (
    'type' => 'class',
    'classname' => 'Floor',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Floor',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Gcd' => 
  array (
    'type' => 'class',
    'classname' => 'Gcd',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Gcd',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Helpers' => 
  array (
    'type' => 'class',
    'classname' => 'Helpers',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Helpers',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\IntClass' => 
  array (
    'type' => 'class',
    'classname' => 'IntClass',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\IntClass',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Lcm' => 
  array (
    'type' => 'class',
    'classname' => 'Lcm',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Lcm',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Logarithms' => 
  array (
    'type' => 'class',
    'classname' => 'Logarithms',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Logarithms',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\MatrixFunctions' => 
  array (
    'type' => 'class',
    'classname' => 'MatrixFunctions',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\MatrixFunctions',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Operations' => 
  array (
    'type' => 'class',
    'classname' => 'Operations',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Operations',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Random' => 
  array (
    'type' => 'class',
    'classname' => 'Random',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Random',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Roman' => 
  array (
    'type' => 'class',
    'classname' => 'Roman',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Roman',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Round' => 
  array (
    'type' => 'class',
    'classname' => 'Round',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Round',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\SeriesSum' => 
  array (
    'type' => 'class',
    'classname' => 'SeriesSum',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\SeriesSum',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Sign' => 
  array (
    'type' => 'class',
    'classname' => 'Sign',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Sign',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Sqrt' => 
  array (
    'type' => 'class',
    'classname' => 'Sqrt',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Sqrt',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Subtotal' => 
  array (
    'type' => 'class',
    'classname' => 'Subtotal',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Subtotal',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Sum' => 
  array (
    'type' => 'class',
    'classname' => 'Sum',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Sum',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\SumSquares' => 
  array (
    'type' => 'class',
    'classname' => 'SumSquares',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\SumSquares',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Trig\\Cosecant' => 
  array (
    'type' => 'class',
    'classname' => 'Cosecant',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Trig',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Trig\\Cosecant',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Trig\\Cosine' => 
  array (
    'type' => 'class',
    'classname' => 'Cosine',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Trig',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Trig\\Cosine',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Trig\\Cotangent' => 
  array (
    'type' => 'class',
    'classname' => 'Cotangent',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Trig',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Trig\\Cotangent',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Trig\\Secant' => 
  array (
    'type' => 'class',
    'classname' => 'Secant',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Trig',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Trig\\Secant',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Trig\\Sine' => 
  array (
    'type' => 'class',
    'classname' => 'Sine',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Trig',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Trig\\Sine',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Trig\\Tangent' => 
  array (
    'type' => 'class',
    'classname' => 'Tangent',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Trig',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Trig\\Tangent',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Trunc' => 
  array (
    'type' => 'class',
    'classname' => 'Trunc',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\MathTrig\\Trunc',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\AggregateBase' => 
  array (
    'type' => 'class',
    'classname' => 'AggregateBase',
    'isabstract' => true,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\AggregateBase',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Averages\\Mean' => 
  array (
    'type' => 'class',
    'classname' => 'Mean',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Averages',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Averages\\Mean',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Conditional' => 
  array (
    'type' => 'class',
    'classname' => 'Conditional',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Conditional',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Confidence' => 
  array (
    'type' => 'class',
    'classname' => 'Confidence',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Confidence',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Counts' => 
  array (
    'type' => 'class',
    'classname' => 'Counts',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Counts',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Deviations' => 
  array (
    'type' => 'class',
    'classname' => 'Deviations',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Deviations',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions\\Beta' => 
  array (
    'type' => 'class',
    'classname' => 'Beta',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions\\Beta',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions\\Binomial' => 
  array (
    'type' => 'class',
    'classname' => 'Binomial',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions\\Binomial',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions\\ChiSquared' => 
  array (
    'type' => 'class',
    'classname' => 'ChiSquared',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions\\ChiSquared',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions\\DistributionValidations' => 
  array (
    'type' => 'class',
    'classname' => 'DistributionValidations',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions\\DistributionValidations',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions\\Exponential' => 
  array (
    'type' => 'class',
    'classname' => 'Exponential',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions\\Exponential',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions\\F' => 
  array (
    'type' => 'class',
    'classname' => 'F',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions\\F',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions\\Fisher' => 
  array (
    'type' => 'class',
    'classname' => 'Fisher',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions\\Fisher',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions\\Gamma' => 
  array (
    'type' => 'class',
    'classname' => 'Gamma',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions\\Gamma',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions\\GammaBase' => 
  array (
    'type' => 'class',
    'classname' => 'GammaBase',
    'isabstract' => true,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions\\GammaBase',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions\\HyperGeometric' => 
  array (
    'type' => 'class',
    'classname' => 'HyperGeometric',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions\\HyperGeometric',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions\\LogNormal' => 
  array (
    'type' => 'class',
    'classname' => 'LogNormal',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions\\LogNormal',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions\\NewtonRaphson' => 
  array (
    'type' => 'class',
    'classname' => 'NewtonRaphson',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions\\NewtonRaphson',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions\\Normal' => 
  array (
    'type' => 'class',
    'classname' => 'Normal',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions\\Normal',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions\\Poisson' => 
  array (
    'type' => 'class',
    'classname' => 'Poisson',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions\\Poisson',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions\\StandardNormal' => 
  array (
    'type' => 'class',
    'classname' => 'StandardNormal',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions\\StandardNormal',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions\\StudentT' => 
  array (
    'type' => 'class',
    'classname' => 'StudentT',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions\\StudentT',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions\\Weibull' => 
  array (
    'type' => 'class',
    'classname' => 'Weibull',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Distributions\\Weibull',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\MaxMinBase' => 
  array (
    'type' => 'class',
    'classname' => 'MaxMinBase',
    'isabstract' => true,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\MaxMinBase',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Maximum' => 
  array (
    'type' => 'class',
    'classname' => 'Maximum',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Maximum',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Minimum' => 
  array (
    'type' => 'class',
    'classname' => 'Minimum',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Minimum',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Percentiles' => 
  array (
    'type' => 'class',
    'classname' => 'Percentiles',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Percentiles',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Permutations' => 
  array (
    'type' => 'class',
    'classname' => 'Permutations',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Permutations',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Size' => 
  array (
    'type' => 'class',
    'classname' => 'Size',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Size',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\StandardDeviations' => 
  array (
    'type' => 'class',
    'classname' => 'StandardDeviations',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\StandardDeviations',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Standardize' => 
  array (
    'type' => 'class',
    'classname' => 'Standardize',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Standardize',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\StatisticalValidations' => 
  array (
    'type' => 'class',
    'classname' => 'StatisticalValidations',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\StatisticalValidations',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Trends' => 
  array (
    'type' => 'class',
    'classname' => 'Trends',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Trends',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\VarianceBase' => 
  array (
    'type' => 'class',
    'classname' => 'VarianceBase',
    'isabstract' => true,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\VarianceBase',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Variances' => 
  array (
    'type' => 'class',
    'classname' => 'Variances',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Statistical\\Variances',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\TextData\\CaseConvert' => 
  array (
    'type' => 'class',
    'classname' => 'CaseConvert',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\TextData',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\TextData\\CaseConvert',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\TextData\\CharacterConvert' => 
  array (
    'type' => 'class',
    'classname' => 'CharacterConvert',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\TextData',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\TextData\\CharacterConvert',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\TextData\\Concatenate' => 
  array (
    'type' => 'class',
    'classname' => 'Concatenate',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\TextData',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\TextData\\Concatenate',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\TextData\\Extract' => 
  array (
    'type' => 'class',
    'classname' => 'Extract',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\TextData',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\TextData\\Extract',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\TextData\\Format' => 
  array (
    'type' => 'class',
    'classname' => 'Format',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\TextData',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\TextData\\Format',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\TextData\\Helpers' => 
  array (
    'type' => 'class',
    'classname' => 'Helpers',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\TextData',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\TextData\\Helpers',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\TextData\\Replace' => 
  array (
    'type' => 'class',
    'classname' => 'Replace',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\TextData',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\TextData\\Replace',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\TextData\\Search' => 
  array (
    'type' => 'class',
    'classname' => 'Search',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\TextData',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\TextData\\Search',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\TextData\\Text' => 
  array (
    'type' => 'class',
    'classname' => 'Text',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\TextData',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\TextData\\Text',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\TextData\\Trim' => 
  array (
    'type' => 'class',
    'classname' => 'Trim',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\TextData',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\TextData\\Trim',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Token\\Stack' => 
  array (
    'type' => 'class',
    'classname' => 'Stack',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Token',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Token\\Stack',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Web\\Service' => 
  array (
    'type' => 'class',
    'classname' => 'Service',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Web',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Web\\Service',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Cell\\AddressHelper' => 
  array (
    'type' => 'class',
    'classname' => 'AddressHelper',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Cell',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Cell\\AddressHelper',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Cell\\AdvancedValueBinder' => 
  array (
    'type' => 'class',
    'classname' => 'AdvancedValueBinder',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Cell',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Cell\\AdvancedValueBinder',
    'implements' => 
    array (
      0 => 'PhpOffice\\PhpSpreadsheet\\Cell\\IValueBinder',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Cell\\Cell' => 
  array (
    'type' => 'class',
    'classname' => 'Cell',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Cell',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Cell\\Cell',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Cell\\CellAddress' => 
  array (
    'type' => 'class',
    'classname' => 'CellAddress',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Cell',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Cell\\CellAddress',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Cell\\CellRange' => 
  array (
    'type' => 'class',
    'classname' => 'CellRange',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Cell',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Cell\\CellRange',
    'implements' => 
    array (
      0 => 'PhpOffice\\PhpSpreadsheet\\Cell\\AddressRange',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Cell\\ColumnRange' => 
  array (
    'type' => 'class',
    'classname' => 'ColumnRange',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Cell',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Cell\\ColumnRange',
    'implements' => 
    array (
      0 => 'PhpOffice\\PhpSpreadsheet\\Cell\\AddressRange',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Cell\\Coordinate' => 
  array (
    'type' => 'class',
    'classname' => 'Coordinate',
    'isabstract' => true,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Cell',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Cell\\Coordinate',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Cell\\DataType' => 
  array (
    'type' => 'class',
    'classname' => 'DataType',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Cell',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Cell\\DataType',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Cell\\DataValidation' => 
  array (
    'type' => 'class',
    'classname' => 'DataValidation',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Cell',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Cell\\DataValidation',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Cell\\DataValidator' => 
  array (
    'type' => 'class',
    'classname' => 'DataValidator',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Cell',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Cell\\DataValidator',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Cell\\DefaultValueBinder' => 
  array (
    'type' => 'class',
    'classname' => 'DefaultValueBinder',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Cell',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Cell\\DefaultValueBinder',
    'implements' => 
    array (
      0 => 'PhpOffice\\PhpSpreadsheet\\Cell\\IValueBinder',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Cell\\Hyperlink' => 
  array (
    'type' => 'class',
    'classname' => 'Hyperlink',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Cell',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Cell\\Hyperlink',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Cell\\IgnoredErrors' => 
  array (
    'type' => 'class',
    'classname' => 'IgnoredErrors',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Cell',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Cell\\IgnoredErrors',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Cell\\RowRange' => 
  array (
    'type' => 'class',
    'classname' => 'RowRange',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Cell',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Cell\\RowRange',
    'implements' => 
    array (
      0 => 'PhpOffice\\PhpSpreadsheet\\Cell\\AddressRange',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Cell\\StringValueBinder' => 
  array (
    'type' => 'class',
    'classname' => 'StringValueBinder',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Cell',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Cell\\StringValueBinder',
    'implements' => 
    array (
      0 => 'PhpOffice\\PhpSpreadsheet\\Cell\\IValueBinder',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\CellReferenceHelper' => 
  array (
    'type' => 'class',
    'classname' => 'CellReferenceHelper',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\CellReferenceHelper',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Chart\\Axis' => 
  array (
    'type' => 'class',
    'classname' => 'Axis',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Chart',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Chart\\Axis',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Chart\\AxisText' => 
  array (
    'type' => 'class',
    'classname' => 'AxisText',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Chart',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Chart\\AxisText',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Chart\\Chart' => 
  array (
    'type' => 'class',
    'classname' => 'Chart',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Chart',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Chart\\Chart',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Chart\\ChartColor' => 
  array (
    'type' => 'class',
    'classname' => 'ChartColor',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Chart',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Chart\\ChartColor',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Chart\\DataSeries' => 
  array (
    'type' => 'class',
    'classname' => 'DataSeries',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Chart',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Chart\\DataSeries',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Chart\\DataSeriesValues' => 
  array (
    'type' => 'class',
    'classname' => 'DataSeriesValues',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Chart',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Chart\\DataSeriesValues',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Chart\\Exception' => 
  array (
    'type' => 'class',
    'classname' => 'Exception',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Chart',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Chart\\Exception',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Chart\\GridLines' => 
  array (
    'type' => 'class',
    'classname' => 'GridLines',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Chart',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Chart\\GridLines',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Chart\\Layout' => 
  array (
    'type' => 'class',
    'classname' => 'Layout',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Chart',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Chart\\Layout',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Chart\\Legend' => 
  array (
    'type' => 'class',
    'classname' => 'Legend',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Chart',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Chart\\Legend',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Chart\\PlotArea' => 
  array (
    'type' => 'class',
    'classname' => 'PlotArea',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Chart',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Chart\\PlotArea',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Chart\\Properties' => 
  array (
    'type' => 'class',
    'classname' => 'Properties',
    'isabstract' => true,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Chart',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Chart\\Properties',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Chart\\Renderer\\JpGraph' => 
  array (
    'type' => 'class',
    'classname' => 'JpGraph',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Chart\\Renderer',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Chart\\Renderer\\JpGraph',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Chart\\Renderer\\JpGraphRendererBase' => 
  array (
    'type' => 'class',
    'classname' => 'JpGraphRendererBase',
    'isabstract' => true,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Chart\\Renderer',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Chart\\Renderer\\JpGraphRendererBase',
    'implements' => 
    array (
      0 => 'PhpOffice\\PhpSpreadsheet\\Chart\\Renderer\\IRenderer',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Chart\\Renderer\\MtJpGraphRenderer' => 
  array (
    'type' => 'class',
    'classname' => 'MtJpGraphRenderer',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Chart\\Renderer',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Chart\\Renderer\\MtJpGraphRenderer',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Chart\\Title' => 
  array (
    'type' => 'class',
    'classname' => 'Title',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Chart',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Chart\\Title',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Chart\\TrendLine' => 
  array (
    'type' => 'class',
    'classname' => 'TrendLine',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Chart',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Chart\\TrendLine',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Collection\\Cells' => 
  array (
    'type' => 'class',
    'classname' => 'Cells',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Collection',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Collection\\Cells',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Collection\\CellsFactory' => 
  array (
    'type' => 'class',
    'classname' => 'CellsFactory',
    'isabstract' => true,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Collection',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Collection\\CellsFactory',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Collection\\Memory\\SimpleCache1' => 
  array (
    'type' => 'class',
    'classname' => 'SimpleCache1',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Collection\\Memory',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Collection\\Memory\\SimpleCache1',
    'implements' => 
    array (
      0 => 'Psr\\SimpleCache\\CacheInterface',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Collection\\Memory\\SimpleCache3' => 
  array (
    'type' => 'class',
    'classname' => 'SimpleCache3',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Collection\\Memory',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Collection\\Memory\\SimpleCache3',
    'implements' => 
    array (
      0 => 'Psr\\SimpleCache\\CacheInterface',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Comment' => 
  array (
    'type' => 'class',
    'classname' => 'Comment',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Comment',
    'implements' => 
    array (
      0 => 'PhpOffice\\PhpSpreadsheet\\IComparable',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\DefinedName' => 
  array (
    'type' => 'class',
    'classname' => 'DefinedName',
    'isabstract' => true,
    'namespace' => 'PhpOffice\\PhpSpreadsheet',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\DefinedName',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Document\\Properties' => 
  array (
    'type' => 'class',
    'classname' => 'Properties',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Document',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Document\\Properties',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Document\\Security' => 
  array (
    'type' => 'class',
    'classname' => 'Security',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Document',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Document\\Security',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Exception' => 
  array (
    'type' => 'class',
    'classname' => 'Exception',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Exception',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\HashTable' => 
  array (
    'type' => 'class',
    'classname' => 'HashTable',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\HashTable',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Helper\\Dimension' => 
  array (
    'type' => 'class',
    'classname' => 'Dimension',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Helper',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Helper\\Dimension',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Helper\\Downloader' => 
  array (
    'type' => 'class',
    'classname' => 'Downloader',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Helper',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Helper\\Downloader',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Helper\\Handler' => 
  array (
    'type' => 'class',
    'classname' => 'Handler',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Helper',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Helper\\Handler',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Helper\\Html' => 
  array (
    'type' => 'class',
    'classname' => 'Html',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Helper',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Helper\\Html',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Helper\\Sample' => 
  array (
    'type' => 'class',
    'classname' => 'Sample',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Helper',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Helper\\Sample',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Helper\\Size' => 
  array (
    'type' => 'class',
    'classname' => 'Size',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Helper',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Helper\\Size',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Helper\\TextGrid' => 
  array (
    'type' => 'class',
    'classname' => 'TextGrid',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Helper',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Helper\\TextGrid',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\IOFactory' => 
  array (
    'type' => 'class',
    'classname' => 'IOFactory',
    'isabstract' => true,
    'namespace' => 'PhpOffice\\PhpSpreadsheet',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\IOFactory',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\NamedFormula' => 
  array (
    'type' => 'class',
    'classname' => 'NamedFormula',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\NamedFormula',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\NamedRange' => 
  array (
    'type' => 'class',
    'classname' => 'NamedRange',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\NamedRange',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\BaseReader' => 
  array (
    'type' => 'class',
    'classname' => 'BaseReader',
    'isabstract' => true,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\BaseReader',
    'implements' => 
    array (
      0 => 'PhpOffice\\PhpSpreadsheet\\Reader\\IReader',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Csv\\Delimiter' => 
  array (
    'type' => 'class',
    'classname' => 'Delimiter',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Csv',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Csv\\Delimiter',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\DefaultReadFilter' => 
  array (
    'type' => 'class',
    'classname' => 'DefaultReadFilter',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\DefaultReadFilter',
    'implements' => 
    array (
      0 => 'PhpOffice\\PhpSpreadsheet\\Reader\\IReadFilter',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Exception' => 
  array (
    'type' => 'class',
    'classname' => 'Exception',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Exception',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Gnumeric\\PageSetup' => 
  array (
    'type' => 'class',
    'classname' => 'PageSetup',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Gnumeric',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Gnumeric\\PageSetup',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Gnumeric\\Properties' => 
  array (
    'type' => 'class',
    'classname' => 'Properties',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Gnumeric',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Gnumeric\\Properties',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Gnumeric\\Styles' => 
  array (
    'type' => 'class',
    'classname' => 'Styles',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Gnumeric',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Gnumeric\\Styles',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Html' => 
  array (
    'type' => 'class',
    'classname' => 'Html',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Html',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Ods\\AutoFilter' => 
  array (
    'type' => 'class',
    'classname' => 'AutoFilter',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Ods',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Ods\\AutoFilter',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Ods\\BaseLoader' => 
  array (
    'type' => 'class',
    'classname' => 'BaseLoader',
    'isabstract' => true,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Ods',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Ods\\BaseLoader',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Ods\\DefinedNames' => 
  array (
    'type' => 'class',
    'classname' => 'DefinedNames',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Ods',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Ods\\DefinedNames',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Ods\\FormulaTranslator' => 
  array (
    'type' => 'class',
    'classname' => 'FormulaTranslator',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Ods',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Ods\\FormulaTranslator',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Ods\\PageSettings' => 
  array (
    'type' => 'class',
    'classname' => 'PageSettings',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Ods',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Ods\\PageSettings',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Ods\\Properties' => 
  array (
    'type' => 'class',
    'classname' => 'Properties',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Ods',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Ods\\Properties',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Security\\XmlScanner' => 
  array (
    'type' => 'class',
    'classname' => 'XmlScanner',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Security',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Security\\XmlScanner',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Slk' => 
  array (
    'type' => 'class',
    'classname' => 'Slk',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Slk',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xls\\Color\\BIFF5' => 
  array (
    'type' => 'class',
    'classname' => 'BIFF5',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xls\\Color',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xls\\Color\\BIFF5',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xls\\Color\\BIFF8' => 
  array (
    'type' => 'class',
    'classname' => 'BIFF8',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xls\\Color',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xls\\Color\\BIFF8',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xls\\Color\\BuiltIn' => 
  array (
    'type' => 'class',
    'classname' => 'BuiltIn',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xls\\Color',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xls\\Color\\BuiltIn',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xls\\ConditionalFormatting' => 
  array (
    'type' => 'class',
    'classname' => 'ConditionalFormatting',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xls',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xls\\ConditionalFormatting',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xls\\DataValidationHelper' => 
  array (
    'type' => 'class',
    'classname' => 'DataValidationHelper',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xls',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xls\\DataValidationHelper',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xls\\ErrorCode' => 
  array (
    'type' => 'class',
    'classname' => 'ErrorCode',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xls',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xls\\ErrorCode',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xls\\Escher' => 
  array (
    'type' => 'class',
    'classname' => 'Escher',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xls',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xls\\Escher',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xls\\MD5' => 
  array (
    'type' => 'class',
    'classname' => 'MD5',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xls',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xls\\MD5',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xls\\RC4' => 
  array (
    'type' => 'class',
    'classname' => 'RC4',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xls',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xls\\RC4',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xls\\Style\\Border' => 
  array (
    'type' => 'class',
    'classname' => 'Border',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xls\\Style',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xls\\Style\\Border',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xls\\Style\\CellAlignment' => 
  array (
    'type' => 'class',
    'classname' => 'CellAlignment',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xls\\Style',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xls\\Style\\CellAlignment',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xls\\Style\\CellFont' => 
  array (
    'type' => 'class',
    'classname' => 'CellFont',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xls\\Style',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xls\\Style\\CellFont',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xls\\Style\\FillPattern' => 
  array (
    'type' => 'class',
    'classname' => 'FillPattern',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xls\\Style',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xls\\Style\\FillPattern',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx\\AutoFilter' => 
  array (
    'type' => 'class',
    'classname' => 'AutoFilter',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx\\AutoFilter',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx\\BaseParserClass' => 
  array (
    'type' => 'class',
    'classname' => 'BaseParserClass',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx\\BaseParserClass',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx\\Chart' => 
  array (
    'type' => 'class',
    'classname' => 'Chart',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx\\Chart',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx\\ColumnAndRowAttributes' => 
  array (
    'type' => 'class',
    'classname' => 'ColumnAndRowAttributes',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx\\ColumnAndRowAttributes',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx\\ConditionalStyles' => 
  array (
    'type' => 'class',
    'classname' => 'ConditionalStyles',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx\\ConditionalStyles',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx\\DataValidations' => 
  array (
    'type' => 'class',
    'classname' => 'DataValidations',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx\\DataValidations',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx\\Hyperlinks' => 
  array (
    'type' => 'class',
    'classname' => 'Hyperlinks',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx\\Hyperlinks',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx\\Namespaces' => 
  array (
    'type' => 'class',
    'classname' => 'Namespaces',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx\\Namespaces',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx\\PageSetup' => 
  array (
    'type' => 'class',
    'classname' => 'PageSetup',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx\\PageSetup',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx\\Properties' => 
  array (
    'type' => 'class',
    'classname' => 'Properties',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx\\Properties',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx\\SharedFormula' => 
  array (
    'type' => 'class',
    'classname' => 'SharedFormula',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx\\SharedFormula',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx\\SheetViewOptions' => 
  array (
    'type' => 'class',
    'classname' => 'SheetViewOptions',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx\\SheetViewOptions',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx\\SheetViews' => 
  array (
    'type' => 'class',
    'classname' => 'SheetViews',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx\\SheetViews',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx\\Styles' => 
  array (
    'type' => 'class',
    'classname' => 'Styles',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx\\Styles',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx\\TableReader' => 
  array (
    'type' => 'class',
    'classname' => 'TableReader',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx\\TableReader',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx\\Theme' => 
  array (
    'type' => 'class',
    'classname' => 'Theme',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx\\Theme',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx\\WorkbookView' => 
  array (
    'type' => 'class',
    'classname' => 'WorkbookView',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xlsx\\WorkbookView',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xml\\DataValidations' => 
  array (
    'type' => 'class',
    'classname' => 'DataValidations',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xml',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xml\\DataValidations',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xml\\PageSettings' => 
  array (
    'type' => 'class',
    'classname' => 'PageSettings',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xml',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xml\\PageSettings',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xml\\Properties' => 
  array (
    'type' => 'class',
    'classname' => 'Properties',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xml',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xml\\Properties',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xml\\Style\\Alignment' => 
  array (
    'type' => 'class',
    'classname' => 'Alignment',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xml\\Style',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xml\\Style\\Alignment',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xml\\Style\\Border' => 
  array (
    'type' => 'class',
    'classname' => 'Border',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xml\\Style',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xml\\Style\\Border',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xml\\Style\\Fill' => 
  array (
    'type' => 'class',
    'classname' => 'Fill',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xml\\Style',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xml\\Style\\Fill',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xml\\Style\\Font' => 
  array (
    'type' => 'class',
    'classname' => 'Font',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xml\\Style',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xml\\Style\\Font',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xml\\Style\\NumberFormat' => 
  array (
    'type' => 'class',
    'classname' => 'NumberFormat',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xml\\Style',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xml\\Style\\NumberFormat',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\Xml\\Style\\StyleBase' => 
  array (
    'type' => 'class',
    'classname' => 'StyleBase',
    'isabstract' => true,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader\\Xml\\Style',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\Xml\\Style\\StyleBase',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\ReferenceHelper' => 
  array (
    'type' => 'class',
    'classname' => 'ReferenceHelper',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\ReferenceHelper',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\RichText\\RichText' => 
  array (
    'type' => 'class',
    'classname' => 'RichText',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\RichText',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\RichText\\RichText',
    'implements' => 
    array (
      0 => 'PhpOffice\\PhpSpreadsheet\\IComparable',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\RichText\\Run' => 
  array (
    'type' => 'class',
    'classname' => 'Run',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\RichText',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\RichText\\Run',
    'implements' => 
    array (
      0 => 'PhpOffice\\PhpSpreadsheet\\RichText\\ITextElement',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\RichText\\TextElement' => 
  array (
    'type' => 'class',
    'classname' => 'TextElement',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\RichText',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\RichText\\TextElement',
    'implements' => 
    array (
      0 => 'PhpOffice\\PhpSpreadsheet\\RichText\\ITextElement',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Settings' => 
  array (
    'type' => 'class',
    'classname' => 'Settings',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Settings',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Shared\\CodePage' => 
  array (
    'type' => 'class',
    'classname' => 'CodePage',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Shared',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Shared\\CodePage',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Shared\\Date' => 
  array (
    'type' => 'class',
    'classname' => 'Date',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Shared',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Shared\\Date',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Shared\\Drawing' => 
  array (
    'type' => 'class',
    'classname' => 'Drawing',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Shared',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Shared\\Drawing',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Shared\\Escher\\DgContainer\\SpgrContainer\\SpContainer' => 
  array (
    'type' => 'class',
    'classname' => 'SpContainer',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Shared\\Escher\\DgContainer\\SpgrContainer',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Shared\\Escher\\DgContainer\\SpgrContainer\\SpContainer',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Shared\\Escher\\DggContainer\\BstoreContainer\\BSE\\Blip' => 
  array (
    'type' => 'class',
    'classname' => 'Blip',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Shared\\Escher\\DggContainer\\BstoreContainer\\BSE',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Shared\\Escher\\DggContainer\\BstoreContainer\\BSE\\Blip',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Shared\\File' => 
  array (
    'type' => 'class',
    'classname' => 'File',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Shared',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Shared\\File',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Shared\\Font' => 
  array (
    'type' => 'class',
    'classname' => 'Font',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Shared',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Shared\\Font',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Shared\\IntOrFloat' => 
  array (
    'type' => 'class',
    'classname' => 'IntOrFloat',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Shared',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Shared\\IntOrFloat',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Shared\\OLE\\ChainedBlockStream' => 
  array (
    'type' => 'class',
    'classname' => 'ChainedBlockStream',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Shared\\OLE',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Shared\\OLE\\ChainedBlockStream',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Shared\\OLE\\PPS\\File' => 
  array (
    'type' => 'class',
    'classname' => 'File',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Shared\\OLE\\PPS',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Shared\\OLE\\PPS\\File',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Shared\\OLE\\PPS\\Root' => 
  array (
    'type' => 'class',
    'classname' => 'Root',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Shared\\OLE\\PPS',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Shared\\OLE\\PPS\\Root',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Shared\\OLERead' => 
  array (
    'type' => 'class',
    'classname' => 'OLERead',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Shared',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Shared\\OLERead',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Shared\\PasswordHasher' => 
  array (
    'type' => 'class',
    'classname' => 'PasswordHasher',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Shared',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Shared\\PasswordHasher',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Shared\\StringHelper' => 
  array (
    'type' => 'class',
    'classname' => 'StringHelper',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Shared',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Shared\\StringHelper',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Shared\\TimeZone' => 
  array (
    'type' => 'class',
    'classname' => 'TimeZone',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Shared',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Shared\\TimeZone',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Shared\\Trend\\BestFit' => 
  array (
    'type' => 'class',
    'classname' => 'BestFit',
    'isabstract' => true,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Shared\\Trend',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Shared\\Trend\\BestFit',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Shared\\Trend\\ExponentialBestFit' => 
  array (
    'type' => 'class',
    'classname' => 'ExponentialBestFit',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Shared\\Trend',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Shared\\Trend\\ExponentialBestFit',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Shared\\Trend\\LinearBestFit' => 
  array (
    'type' => 'class',
    'classname' => 'LinearBestFit',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Shared\\Trend',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Shared\\Trend\\LinearBestFit',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Shared\\Trend\\LogarithmicBestFit' => 
  array (
    'type' => 'class',
    'classname' => 'LogarithmicBestFit',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Shared\\Trend',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Shared\\Trend\\LogarithmicBestFit',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Shared\\Trend\\PolynomialBestFit' => 
  array (
    'type' => 'class',
    'classname' => 'PolynomialBestFit',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Shared\\Trend',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Shared\\Trend\\PolynomialBestFit',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Shared\\Trend\\PowerBestFit' => 
  array (
    'type' => 'class',
    'classname' => 'PowerBestFit',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Shared\\Trend',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Shared\\Trend\\PowerBestFit',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Shared\\Trend\\Trend' => 
  array (
    'type' => 'class',
    'classname' => 'Trend',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Shared\\Trend',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Shared\\Trend\\Trend',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Shared\\XMLWriter' => 
  array (
    'type' => 'class',
    'classname' => 'XMLWriter',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Shared',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Shared\\XMLWriter',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Shared\\Xls' => 
  array (
    'type' => 'class',
    'classname' => 'Xls',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Shared',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Shared\\Xls',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Spreadsheet' => 
  array (
    'type' => 'class',
    'classname' => 'Spreadsheet',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Spreadsheet',
    'implements' => 
    array (
      0 => 'JsonSerializable',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\Alignment' => 
  array (
    'type' => 'class',
    'classname' => 'Alignment',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\Alignment',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\Border' => 
  array (
    'type' => 'class',
    'classname' => 'Border',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\Border',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\Borders' => 
  array (
    'type' => 'class',
    'classname' => 'Borders',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\Borders',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\Color' => 
  array (
    'type' => 'class',
    'classname' => 'Color',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\Color',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\Conditional' => 
  array (
    'type' => 'class',
    'classname' => 'Conditional',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\Conditional',
    'implements' => 
    array (
      0 => 'PhpOffice\\PhpSpreadsheet\\IComparable',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\CellMatcher' => 
  array (
    'type' => 'class',
    'classname' => 'CellMatcher',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\CellMatcher',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\CellStyleAssessor' => 
  array (
    'type' => 'class',
    'classname' => 'CellStyleAssessor',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\CellStyleAssessor',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\ConditionalDataBar' => 
  array (
    'type' => 'class',
    'classname' => 'ConditionalDataBar',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\ConditionalDataBar',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\ConditionalDataBarExtension' => 
  array (
    'type' => 'class',
    'classname' => 'ConditionalDataBarExtension',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\ConditionalDataBarExtension',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\ConditionalFormatValueObject' => 
  array (
    'type' => 'class',
    'classname' => 'ConditionalFormatValueObject',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\ConditionalFormatValueObject',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\ConditionalFormattingRuleExtension' => 
  array (
    'type' => 'class',
    'classname' => 'ConditionalFormattingRuleExtension',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\ConditionalFormattingRuleExtension',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\StyleMerger' => 
  array (
    'type' => 'class',
    'classname' => 'StyleMerger',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\StyleMerger',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\Wizard\\Blanks' => 
  array (
    'type' => 'class',
    'classname' => 'Blanks',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\Wizard',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\Wizard\\Blanks',
    'implements' => 
    array (
      0 => 'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\Wizard\\WizardInterface',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\Wizard\\CellValue' => 
  array (
    'type' => 'class',
    'classname' => 'CellValue',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\Wizard',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\Wizard\\CellValue',
    'implements' => 
    array (
      0 => 'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\Wizard\\WizardInterface',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\Wizard\\DateValue' => 
  array (
    'type' => 'class',
    'classname' => 'DateValue',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\Wizard',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\Wizard\\DateValue',
    'implements' => 
    array (
      0 => 'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\Wizard\\WizardInterface',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\Wizard\\Duplicates' => 
  array (
    'type' => 'class',
    'classname' => 'Duplicates',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\Wizard',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\Wizard\\Duplicates',
    'implements' => 
    array (
      0 => 'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\Wizard\\WizardInterface',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\Wizard\\Errors' => 
  array (
    'type' => 'class',
    'classname' => 'Errors',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\Wizard',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\Wizard\\Errors',
    'implements' => 
    array (
      0 => 'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\Wizard\\WizardInterface',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\Wizard\\Expression' => 
  array (
    'type' => 'class',
    'classname' => 'Expression',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\Wizard',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\Wizard\\Expression',
    'implements' => 
    array (
      0 => 'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\Wizard\\WizardInterface',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\Wizard\\TextValue' => 
  array (
    'type' => 'class',
    'classname' => 'TextValue',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\Wizard',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\Wizard\\TextValue',
    'implements' => 
    array (
      0 => 'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\Wizard\\WizardInterface',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\Wizard\\WizardAbstract' => 
  array (
    'type' => 'class',
    'classname' => 'WizardAbstract',
    'isabstract' => true,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\Wizard',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\Wizard\\WizardAbstract',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\Fill' => 
  array (
    'type' => 'class',
    'classname' => 'Fill',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\Fill',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\Font' => 
  array (
    'type' => 'class',
    'classname' => 'Font',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\Font',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\BaseFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'BaseFormatter',
    'isabstract' => true,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\BaseFormatter',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\DateFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'DateFormatter',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\DateFormatter',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Formatter' => 
  array (
    'type' => 'class',
    'classname' => 'Formatter',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Formatter',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\FractionFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'FractionFormatter',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\FractionFormatter',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\NumberFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'NumberFormatter',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\NumberFormatter',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\PercentageFormatter' => 
  array (
    'type' => 'class',
    'classname' => 'PercentageFormatter',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\PercentageFormatter',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard\\Accounting' => 
  array (
    'type' => 'class',
    'classname' => 'Accounting',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard\\Accounting',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard\\Currency' => 
  array (
    'type' => 'class',
    'classname' => 'Currency',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard\\Currency',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard\\Date' => 
  array (
    'type' => 'class',
    'classname' => 'Date',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard\\Date',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard\\DateTime' => 
  array (
    'type' => 'class',
    'classname' => 'DateTime',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard\\DateTime',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard\\DateTimeWizard' => 
  array (
    'type' => 'class',
    'classname' => 'DateTimeWizard',
    'isabstract' => true,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard\\DateTimeWizard',
    'implements' => 
    array (
      0 => 'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard\\Wizard',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard\\Duration' => 
  array (
    'type' => 'class',
    'classname' => 'Duration',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard\\Duration',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard\\Locale' => 
  array (
    'type' => 'class',
    'classname' => 'Locale',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard\\Locale',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard\\Number' => 
  array (
    'type' => 'class',
    'classname' => 'Number',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard\\Number',
    'implements' => 
    array (
      0 => 'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard\\Wizard',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard\\NumberBase' => 
  array (
    'type' => 'class',
    'classname' => 'NumberBase',
    'isabstract' => true,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard\\NumberBase',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard\\Percentage' => 
  array (
    'type' => 'class',
    'classname' => 'Percentage',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard\\Percentage',
    'implements' => 
    array (
      0 => 'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard\\Wizard',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard\\Scientific' => 
  array (
    'type' => 'class',
    'classname' => 'Scientific',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard\\Scientific',
    'implements' => 
    array (
      0 => 'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard\\Wizard',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard\\Time' => 
  array (
    'type' => 'class',
    'classname' => 'Time',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard\\Time',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\Protection' => 
  array (
    'type' => 'class',
    'classname' => 'Protection',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\Protection',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\RgbTint' => 
  array (
    'type' => 'class',
    'classname' => 'RgbTint',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\RgbTint',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\Style' => 
  array (
    'type' => 'class',
    'classname' => 'Style',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\Style',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\Supervisor' => 
  array (
    'type' => 'class',
    'classname' => 'Supervisor',
    'isabstract' => true,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\Supervisor',
    'implements' => 
    array (
      0 => 'PhpOffice\\PhpSpreadsheet\\IComparable',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Theme' => 
  array (
    'type' => 'class',
    'classname' => 'Theme',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Theme',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Worksheet\\AutoFilter\\Column\\Rule' => 
  array (
    'type' => 'class',
    'classname' => 'Rule',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Worksheet\\AutoFilter\\Column',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Worksheet\\AutoFilter\\Column\\Rule',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Worksheet\\AutoFit' => 
  array (
    'type' => 'class',
    'classname' => 'AutoFit',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Worksheet',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Worksheet\\AutoFit',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Worksheet\\BaseDrawing' => 
  array (
    'type' => 'class',
    'classname' => 'BaseDrawing',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Worksheet',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Worksheet\\BaseDrawing',
    'implements' => 
    array (
      0 => 'PhpOffice\\PhpSpreadsheet\\IComparable',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Worksheet\\CellIterator' => 
  array (
    'type' => 'class',
    'classname' => 'CellIterator',
    'isabstract' => true,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Worksheet',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Worksheet\\CellIterator',
    'implements' => 
    array (
      0 => 'Iterator',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Worksheet\\Column' => 
  array (
    'type' => 'class',
    'classname' => 'Column',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Worksheet',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Worksheet\\Column',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Worksheet\\ColumnCellIterator' => 
  array (
    'type' => 'class',
    'classname' => 'ColumnCellIterator',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Worksheet',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Worksheet\\ColumnCellIterator',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Worksheet\\ColumnDimension' => 
  array (
    'type' => 'class',
    'classname' => 'ColumnDimension',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Worksheet',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Worksheet\\ColumnDimension',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Worksheet\\ColumnIterator' => 
  array (
    'type' => 'class',
    'classname' => 'ColumnIterator',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Worksheet',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Worksheet\\ColumnIterator',
    'implements' => 
    array (
      0 => 'Iterator',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Worksheet\\Dimension' => 
  array (
    'type' => 'class',
    'classname' => 'Dimension',
    'isabstract' => true,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Worksheet',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Worksheet\\Dimension',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Worksheet\\Drawing\\Shadow' => 
  array (
    'type' => 'class',
    'classname' => 'Shadow',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Worksheet\\Drawing',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Worksheet\\Drawing\\Shadow',
    'implements' => 
    array (
      0 => 'PhpOffice\\PhpSpreadsheet\\IComparable',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Worksheet\\HeaderFooter' => 
  array (
    'type' => 'class',
    'classname' => 'HeaderFooter',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Worksheet',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Worksheet\\HeaderFooter',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Worksheet\\HeaderFooterDrawing' => 
  array (
    'type' => 'class',
    'classname' => 'HeaderFooterDrawing',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Worksheet',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Worksheet\\HeaderFooterDrawing',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Worksheet\\Iterator' => 
  array (
    'type' => 'class',
    'classname' => 'Iterator',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Worksheet',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Worksheet\\Iterator',
    'implements' => 
    array (
      0 => 'Iterator',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Worksheet\\MemoryDrawing' => 
  array (
    'type' => 'class',
    'classname' => 'MemoryDrawing',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Worksheet',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Worksheet\\MemoryDrawing',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Worksheet\\PageBreak' => 
  array (
    'type' => 'class',
    'classname' => 'PageBreak',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Worksheet',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Worksheet\\PageBreak',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Worksheet\\PageMargins' => 
  array (
    'type' => 'class',
    'classname' => 'PageMargins',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Worksheet',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Worksheet\\PageMargins',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Worksheet\\PageSetup' => 
  array (
    'type' => 'class',
    'classname' => 'PageSetup',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Worksheet',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Worksheet\\PageSetup',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Worksheet\\Protection' => 
  array (
    'type' => 'class',
    'classname' => 'Protection',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Worksheet',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Worksheet\\Protection',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Worksheet\\Row' => 
  array (
    'type' => 'class',
    'classname' => 'Row',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Worksheet',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Worksheet\\Row',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Worksheet\\RowCellIterator' => 
  array (
    'type' => 'class',
    'classname' => 'RowCellIterator',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Worksheet',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Worksheet\\RowCellIterator',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Worksheet\\RowDimension' => 
  array (
    'type' => 'class',
    'classname' => 'RowDimension',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Worksheet',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Worksheet\\RowDimension',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Worksheet\\RowIterator' => 
  array (
    'type' => 'class',
    'classname' => 'RowIterator',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Worksheet',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Worksheet\\RowIterator',
    'implements' => 
    array (
      0 => 'Iterator',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Worksheet\\SheetView' => 
  array (
    'type' => 'class',
    'classname' => 'SheetView',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Worksheet',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Worksheet\\SheetView',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Worksheet\\Table\\Column' => 
  array (
    'type' => 'class',
    'classname' => 'Column',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Worksheet\\Table',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Worksheet\\Table\\Column',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Worksheet\\Table\\TableStyle' => 
  array (
    'type' => 'class',
    'classname' => 'TableStyle',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Worksheet\\Table',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Worksheet\\Table\\TableStyle',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Worksheet\\Validations' => 
  array (
    'type' => 'class',
    'classname' => 'Validations',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Worksheet',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Worksheet\\Validations',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Worksheet\\Worksheet' => 
  array (
    'type' => 'class',
    'classname' => 'Worksheet',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Worksheet',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Worksheet\\Worksheet',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\BaseWriter' => 
  array (
    'type' => 'class',
    'classname' => 'BaseWriter',
    'isabstract' => true,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\BaseWriter',
    'implements' => 
    array (
      0 => 'PhpOffice\\PhpSpreadsheet\\Writer\\IWriter',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Csv' => 
  array (
    'type' => 'class',
    'classname' => 'Csv',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Csv',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Exception' => 
  array (
    'type' => 'class',
    'classname' => 'Exception',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Exception',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Html' => 
  array (
    'type' => 'class',
    'classname' => 'Html',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Html',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Ods\\AutoFilters' => 
  array (
    'type' => 'class',
    'classname' => 'AutoFilters',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Ods',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Ods\\AutoFilters',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Ods\\Cell\\Comment' => 
  array (
    'type' => 'class',
    'classname' => 'Comment',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Ods\\Cell',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Ods\\Cell\\Comment',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Ods\\Cell\\Style' => 
  array (
    'type' => 'class',
    'classname' => 'Style',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Ods\\Cell',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Ods\\Cell\\Style',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Ods\\Content' => 
  array (
    'type' => 'class',
    'classname' => 'Content',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Ods',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Ods\\Content',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Ods\\Formula' => 
  array (
    'type' => 'class',
    'classname' => 'Formula',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Ods',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Ods\\Formula',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Ods\\Meta' => 
  array (
    'type' => 'class',
    'classname' => 'Meta',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Ods',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Ods\\Meta',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Ods\\MetaInf' => 
  array (
    'type' => 'class',
    'classname' => 'MetaInf',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Ods',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Ods\\MetaInf',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Ods\\Mimetype' => 
  array (
    'type' => 'class',
    'classname' => 'Mimetype',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Ods',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Ods\\Mimetype',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Ods\\NamedExpressions' => 
  array (
    'type' => 'class',
    'classname' => 'NamedExpressions',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Ods',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Ods\\NamedExpressions',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Ods\\Settings' => 
  array (
    'type' => 'class',
    'classname' => 'Settings',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Ods',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Ods\\Settings',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Ods\\Styles' => 
  array (
    'type' => 'class',
    'classname' => 'Styles',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Ods',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Ods\\Styles',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Ods\\Thumbnails' => 
  array (
    'type' => 'class',
    'classname' => 'Thumbnails',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Ods',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Ods\\Thumbnails',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Ods\\WriterPart' => 
  array (
    'type' => 'class',
    'classname' => 'WriterPart',
    'isabstract' => true,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Ods',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Ods\\WriterPart',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Pdf\\Dompdf' => 
  array (
    'type' => 'class',
    'classname' => 'Dompdf',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Pdf',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Pdf\\Dompdf',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Pdf\\Mpdf' => 
  array (
    'type' => 'class',
    'classname' => 'Mpdf',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Pdf',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Pdf\\Mpdf',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Pdf\\Tcpdf' => 
  array (
    'type' => 'class',
    'classname' => 'Tcpdf',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Pdf',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Pdf\\Tcpdf',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\BIFFwriter' => 
  array (
    'type' => 'class',
    'classname' => 'BIFFwriter',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Xls',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\BIFFwriter',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\CellDataValidation' => 
  array (
    'type' => 'class',
    'classname' => 'CellDataValidation',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Xls',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\CellDataValidation',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\ConditionalHelper' => 
  array (
    'type' => 'class',
    'classname' => 'ConditionalHelper',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Xls',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\ConditionalHelper',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\ErrorCode' => 
  array (
    'type' => 'class',
    'classname' => 'ErrorCode',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Xls',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\ErrorCode',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\Escher' => 
  array (
    'type' => 'class',
    'classname' => 'Escher',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Xls',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\Escher',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\Font' => 
  array (
    'type' => 'class',
    'classname' => 'Font',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Xls',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\Font',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\Parser' => 
  array (
    'type' => 'class',
    'classname' => 'Parser',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Xls',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\Parser',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\Style\\CellAlignment' => 
  array (
    'type' => 'class',
    'classname' => 'CellAlignment',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\Style',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\Style\\CellAlignment',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\Style\\CellBorder' => 
  array (
    'type' => 'class',
    'classname' => 'CellBorder',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\Style',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\Style\\CellBorder',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\Style\\CellFill' => 
  array (
    'type' => 'class',
    'classname' => 'CellFill',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\Style',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\Style\\CellFill',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\Style\\ColorMap' => 
  array (
    'type' => 'class',
    'classname' => 'ColorMap',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\Style',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\Style\\ColorMap',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\Workbook' => 
  array (
    'type' => 'class',
    'classname' => 'Workbook',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Xls',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\Workbook',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\Worksheet' => 
  array (
    'type' => 'class',
    'classname' => 'Worksheet',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Xls',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\Worksheet',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\Xf' => 
  array (
    'type' => 'class',
    'classname' => 'Xf',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Xls',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Xls\\Xf',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\AutoFilter' => 
  array (
    'type' => 'class',
    'classname' => 'AutoFilter',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\AutoFilter',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\Chart' => 
  array (
    'type' => 'class',
    'classname' => 'Chart',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\Chart',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\Comments' => 
  array (
    'type' => 'class',
    'classname' => 'Comments',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\Comments',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\ContentTypes' => 
  array (
    'type' => 'class',
    'classname' => 'ContentTypes',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\ContentTypes',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\DefinedNames' => 
  array (
    'type' => 'class',
    'classname' => 'DefinedNames',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\DefinedNames',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\DocProps' => 
  array (
    'type' => 'class',
    'classname' => 'DocProps',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\DocProps',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\Drawing' => 
  array (
    'type' => 'class',
    'classname' => 'Drawing',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\Drawing',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\FunctionPrefix' => 
  array (
    'type' => 'class',
    'classname' => 'FunctionPrefix',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\FunctionPrefix',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\Rels' => 
  array (
    'type' => 'class',
    'classname' => 'Rels',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\Rels',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\RelsRibbon' => 
  array (
    'type' => 'class',
    'classname' => 'RelsRibbon',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\RelsRibbon',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\RelsVBA' => 
  array (
    'type' => 'class',
    'classname' => 'RelsVBA',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\RelsVBA',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\StringTable' => 
  array (
    'type' => 'class',
    'classname' => 'StringTable',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\StringTable',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\Style' => 
  array (
    'type' => 'class',
    'classname' => 'Style',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\Style',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\Table' => 
  array (
    'type' => 'class',
    'classname' => 'Table',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\Table',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\Theme' => 
  array (
    'type' => 'class',
    'classname' => 'Theme',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\Theme',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\Workbook' => 
  array (
    'type' => 'class',
    'classname' => 'Workbook',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\Workbook',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\Worksheet' => 
  array (
    'type' => 'class',
    'classname' => 'Worksheet',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\Worksheet',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\WriterPart' => 
  array (
    'type' => 'class',
    'classname' => 'WriterPart',
    'isabstract' => true,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx\\WriterPart',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\ZipStream0' => 
  array (
    'type' => 'class',
    'classname' => 'ZipStream0',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\ZipStream0',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\ZipStream2' => 
  array (
    'type' => 'class',
    'classname' => 'ZipStream2',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\ZipStream2',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\ZipStream3' => 
  array (
    'type' => 'class',
    'classname' => 'ZipStream3',
    'isabstract' => false,
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer',
    'extends' => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\ZipStream3',
    'implements' => 
    array (
    ),
  ),
  'Symfony\\Polyfill\\Mbstring\\Mbstring' => 
  array (
    'type' => 'class',
    'classname' => 'Mbstring',
    'isabstract' => false,
    'namespace' => 'Symfony\\Polyfill\\Mbstring',
    'extends' => 'LearnDash\\Reports\\Symfony\\Polyfill\\Mbstring\\Mbstring',
    'implements' => 
    array (
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\ArrayEnabled' => 
  array (
    'type' => 'trait',
    'traitname' => 'ArrayEnabled',
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation',
    'use' => 
    array (
      0 => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\ArrayEnabled',
    ),
  ),
  'Stringable' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Stringable',
    'namespace' => '\\',
    'extends' => 
    array (
      0 => 'LearnDash_Reports_Stringable',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Calculation\\Engine\\Operands\\Operand' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Operand',
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Calculation\\Engine\\Operands',
    'extends' => 
    array (
      0 => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Calculation\\Engine\\Operands\\Operand',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Cell\\AddressRange' => 
  array (
    'type' => 'interface',
    'interfacename' => 'AddressRange',
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Cell',
    'extends' => 
    array (
      0 => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Cell\\AddressRange',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Cell\\IValueBinder' => 
  array (
    'type' => 'interface',
    'interfacename' => 'IValueBinder',
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Cell',
    'extends' => 
    array (
      0 => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Cell\\IValueBinder',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Chart\\Renderer\\IRenderer' => 
  array (
    'type' => 'interface',
    'interfacename' => 'IRenderer',
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Chart\\Renderer',
    'extends' => 
    array (
      0 => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Chart\\Renderer\\IRenderer',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\IComparable' => 
  array (
    'type' => 'interface',
    'interfacename' => 'IComparable',
    'namespace' => 'PhpOffice\\PhpSpreadsheet',
    'extends' => 
    array (
      0 => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\IComparable',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\IReadFilter' => 
  array (
    'type' => 'interface',
    'interfacename' => 'IReadFilter',
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader',
    'extends' => 
    array (
      0 => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\IReadFilter',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Reader\\IReader' => 
  array (
    'type' => 'interface',
    'interfacename' => 'IReader',
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Reader',
    'extends' => 
    array (
      0 => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Reader\\IReader',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\RichText\\ITextElement' => 
  array (
    'type' => 'interface',
    'interfacename' => 'ITextElement',
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\RichText',
    'extends' => 
    array (
      0 => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\RichText\\ITextElement',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\Wizard\\WizardInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'WizardInterface',
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\Wizard',
    'extends' => 
    array (
      0 => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\ConditionalFormatting\\Wizard\\WizardInterface',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard\\Wizard' => 
  array (
    'type' => 'interface',
    'interfacename' => 'Wizard',
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard',
    'extends' => 
    array (
      0 => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Style\\NumberFormat\\Wizard\\Wizard',
    ),
  ),
  'PhpOffice\\PhpSpreadsheet\\Writer\\IWriter' => 
  array (
    'type' => 'interface',
    'interfacename' => 'IWriter',
    'namespace' => 'PhpOffice\\PhpSpreadsheet\\Writer',
    'extends' => 
    array (
      0 => 'LearnDash\\Reports\\PhpOffice\\PhpSpreadsheet\\Writer\\IWriter',
    ),
  ),
  'Psr\\Http\\Client\\ClientExceptionInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'ClientExceptionInterface',
    'namespace' => 'Psr\\Http\\Client',
    'extends' => 
    array (
      0 => 'LearnDash\\Reports\\Psr\\Http\\Client\\ClientExceptionInterface',
    ),
  ),
  'Psr\\Http\\Client\\ClientInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'ClientInterface',
    'namespace' => 'Psr\\Http\\Client',
    'extends' => 
    array (
      0 => 'LearnDash\\Reports\\Psr\\Http\\Client\\ClientInterface',
    ),
  ),
  'Psr\\Http\\Client\\NetworkExceptionInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'NetworkExceptionInterface',
    'namespace' => 'Psr\\Http\\Client',
    'extends' => 
    array (
      0 => 'LearnDash\\Reports\\Psr\\Http\\Client\\NetworkExceptionInterface',
    ),
  ),
  'Psr\\Http\\Client\\RequestExceptionInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'RequestExceptionInterface',
    'namespace' => 'Psr\\Http\\Client',
    'extends' => 
    array (
      0 => 'LearnDash\\Reports\\Psr\\Http\\Client\\RequestExceptionInterface',
    ),
  ),
  'Psr\\Http\\Message\\RequestFactoryInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'RequestFactoryInterface',
    'namespace' => 'Psr\\Http\\Message',
    'extends' => 
    array (
      0 => 'LearnDash\\Reports\\Psr\\Http\\Message\\RequestFactoryInterface',
    ),
  ),
  'Psr\\Http\\Message\\ResponseFactoryInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'ResponseFactoryInterface',
    'namespace' => 'Psr\\Http\\Message',
    'extends' => 
    array (
      0 => 'LearnDash\\Reports\\Psr\\Http\\Message\\ResponseFactoryInterface',
    ),
  ),
  'Psr\\Http\\Message\\ServerRequestFactoryInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'ServerRequestFactoryInterface',
    'namespace' => 'Psr\\Http\\Message',
    'extends' => 
    array (
      0 => 'LearnDash\\Reports\\Psr\\Http\\Message\\ServerRequestFactoryInterface',
    ),
  ),
  'Psr\\Http\\Message\\StreamFactoryInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'StreamFactoryInterface',
    'namespace' => 'Psr\\Http\\Message',
    'extends' => 
    array (
      0 => 'LearnDash\\Reports\\Psr\\Http\\Message\\StreamFactoryInterface',
    ),
  ),
  'Psr\\Http\\Message\\UploadedFileFactoryInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'UploadedFileFactoryInterface',
    'namespace' => 'Psr\\Http\\Message',
    'extends' => 
    array (
      0 => 'LearnDash\\Reports\\Psr\\Http\\Message\\UploadedFileFactoryInterface',
    ),
  ),
  'Psr\\Http\\Message\\UriFactoryInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'UriFactoryInterface',
    'namespace' => 'Psr\\Http\\Message',
    'extends' => 
    array (
      0 => 'LearnDash\\Reports\\Psr\\Http\\Message\\UriFactoryInterface',
    ),
  ),
  'Psr\\Http\\Message\\MessageInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'MessageInterface',
    'namespace' => 'Psr\\Http\\Message',
    'extends' => 
    array (
      0 => 'LearnDash\\Reports\\Psr\\Http\\Message\\MessageInterface',
    ),
  ),
  'Psr\\Http\\Message\\RequestInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'RequestInterface',
    'namespace' => 'Psr\\Http\\Message',
    'extends' => 
    array (
      0 => 'LearnDash\\Reports\\Psr\\Http\\Message\\RequestInterface',
    ),
  ),
  'Psr\\Http\\Message\\ResponseInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'ResponseInterface',
    'namespace' => 'Psr\\Http\\Message',
    'extends' => 
    array (
      0 => 'LearnDash\\Reports\\Psr\\Http\\Message\\ResponseInterface',
    ),
  ),
  'Psr\\Http\\Message\\ServerRequestInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'ServerRequestInterface',
    'namespace' => 'Psr\\Http\\Message',
    'extends' => 
    array (
      0 => 'LearnDash\\Reports\\Psr\\Http\\Message\\ServerRequestInterface',
    ),
  ),
  'Psr\\Http\\Message\\StreamInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'StreamInterface',
    'namespace' => 'Psr\\Http\\Message',
    'extends' => 
    array (
      0 => 'LearnDash\\Reports\\Psr\\Http\\Message\\StreamInterface',
    ),
  ),
  'Psr\\Http\\Message\\UploadedFileInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'UploadedFileInterface',
    'namespace' => 'Psr\\Http\\Message',
    'extends' => 
    array (
      0 => 'LearnDash\\Reports\\Psr\\Http\\Message\\UploadedFileInterface',
    ),
  ),
  'Psr\\Http\\Message\\UriInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'UriInterface',
    'namespace' => 'Psr\\Http\\Message',
    'extends' => 
    array (
      0 => 'LearnDash\\Reports\\Psr\\Http\\Message\\UriInterface',
    ),
  ),
  'Psr\\SimpleCache\\CacheException' => 
  array (
    'type' => 'interface',
    'interfacename' => 'CacheException',
    'namespace' => 'Psr\\SimpleCache',
    'extends' => 
    array (
      0 => 'LearnDash\\Reports\\Psr\\SimpleCache\\CacheException',
    ),
  ),
  'Psr\\SimpleCache\\CacheInterface' => 
  array (
    'type' => 'interface',
    'interfacename' => 'CacheInterface',
    'namespace' => 'Psr\\SimpleCache',
    'extends' => 
    array (
      0 => 'LearnDash\\Reports\\Psr\\SimpleCache\\CacheInterface',
    ),
  ),
  'Psr\\SimpleCache\\InvalidArgumentException' => 
  array (
    'type' => 'interface',
    'interfacename' => 'InvalidArgumentException',
    'namespace' => 'Psr\\SimpleCache',
    'extends' => 
    array (
      0 => 'LearnDash\\Reports\\Psr\\SimpleCache\\InvalidArgumentException',
    ),
  ),
);

        public function __construct()
        {
            $this->includeFilePath = __DIR__ . '/autoload_alias.php';
        }

        public function autoload($class)
        {
            if (!isset($this->autoloadAliases[$class])) {
                return;
            }
            switch ($this->autoloadAliases[$class]['type']) {
                case 'class':
                        $this->load(
                            $this->classTemplate(
                                $this->autoloadAliases[$class]
                            )
                        );
                    break;
                case 'interface':
                    $this->load(
                        $this->interfaceTemplate(
                            $this->autoloadAliases[$class]
                        )
                    );
                    break;
                case 'trait':
                    $this->load(
                        $this->traitTemplate(
                            $this->autoloadAliases[$class]
                        )
                    );
                    break;
                default:
                    // Never.
                    break;
            }
        }

        private function load(string $includeFile)
        {
            file_put_contents($this->includeFilePath, $includeFile);
            include $this->includeFilePath;
            file_exists($this->includeFilePath) && unlink($this->includeFilePath);
        }

        private function classTemplate(array $class): string
        {
            $abstract = $class['isabstract'] ? 'abstract ' : '';
            $classname = $class['classname'];
            if (isset($class['namespace'])) {
                $namespace = "namespace {$class['namespace']};";
                $extends = '\\' . $class['extends'];
                $implements = empty($class['implements']) ? ''
                : ' implements \\' . implode(', \\', $class['implements']);
            } else {
                $namespace = '';
                $extends = $class['extends'];
                $implements = !empty($class['implements']) ? ''
                : ' implements ' . implode(', ', $class['implements']);
            }
            return <<<EOD
                <?php
                $namespace
                $abstract class $classname extends $extends $implements {}
                EOD;
        }

        private function interfaceTemplate(array $interface): string
        {
            $interfacename = $interface['interfacename'];
            $namespace = isset($interface['namespace'])
            ? "namespace {$interface['namespace']};" : '';
            $extends = isset($interface['namespace'])
            ? '\\' . implode('\\ ,', $interface['extends'])
            : implode(', ', $interface['extends']);
            return <<<EOD
                <?php
                $namespace
                interface $interfacename extends $extends {}
                EOD;
        }
        private function traitTemplate(array $trait): string
        {
            $traitname = $trait['traitname'];
            $namespace = isset($trait['namespace'])
            ? "namespace {$trait['namespace']};" : '';
            $uses = isset($trait['namespace'])
            ? '\\' . implode(';' . PHP_EOL . '    use \\', $trait['use'])
            : implode(';' . PHP_EOL . '    use ', $trait['use']);
            return <<<EOD
                <?php
                $namespace
                trait $traitname { 
                    use $uses; 
                }
                EOD;
        }
    }

    spl_autoload_register([ new AliasAutoloader(), 'autoload' ]);
}
