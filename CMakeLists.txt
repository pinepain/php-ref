cmake_minimum_required(VERSION 3.1)
project(php_ref)

# NOTE: This CMake file is just for syntax highlighting in CLion

include_directories(/usr/local/include/php)
include_directories(/usr/local/include/php/TSRM)
include_directories(/usr/local/include/php/Zend)
include_directories(/usr/local/include/php/ext)
include_directories(/usr/local/include/php/main)
include_directories(/usr/local/include/php/sapi)

add_definitions(-DCOMPILE_DL_REF)

set(SOURCE_FILES
    php_ref.h
    ref.c

    php_ref_notifier_exception.c
    php_ref_notifier_exception.h

    php_ref_reference.c
    php_ref_reference.h

    php_ref_functions.c
    php_ref_functions.h
)

add_executable(php_ref ${SOURCE_FILES})
