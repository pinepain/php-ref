cmake_minimum_required(VERSION 3.1)
project(php_weak)

# NOTE: This CMake file is just for syntax highlighting in CLion

include_directories(./php7debugzts)
include_directories(./php7debugzts/main)
include_directories(./php7debugzts/Zend)
include_directories(./php7debugzts/TSRM)

#include_directories(./tmp/usr/lib/v8/include)

set(CMAKE_CXX_FLAGS "${CMAKE_CXX_FLAGS} -std=c++11")

set(SOURCE_FILES
    php_weak.h
    weak.c

    php_weak_reference.c
    php_weak_reference.h

    php_weak_functions.c
    php_weak_functions.h
)

add_executable(php_weak ${SOURCE_FILES})
