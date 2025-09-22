<?php

require_once 'vendor/autoload.php';

use Jane\Component\JsonSchema\Generator\Context\Context;
use Jane\Component\JsonSchema\Registry;
use Jane\Component\JsonSchema\Schema;
use Jane\Component\JsonSchema\Generator\Generator;
use Jane\Component\JsonSchema\Generator\GeneratorRegistry;
use Jane\Component\JsonSchema\Guesser\Guess\MultipleType;
use Jane\Component\JsonSchema\Guesser\GuesserRegistry;
use Jane\Component\JsonSchema\Guesser\JsonSchema\JsonSchemaGuesser;
use Jane\Component\JsonSchema\Jane;

try {
    $schemaPath = __DIR__ . '/schemas/create-attendee-schema.json';
    $outputDir = __DIR__ . '/src/GeneratedDTOs';
    
    // Create the Jane instance
    $jane = Jane::build();
    
    // Generate the classes
    $jane->generate(
        $schemaPath,
        'App\\GeneratedDTOs',
        $outputDir
    );
    
    echo "DTO generated successfully in $outputDir\n";
    
} catch (Exception $e) {
    echo "Error generating DTO: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}