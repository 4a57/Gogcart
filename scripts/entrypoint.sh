#!/bin/sh

export $(cat .env | grep "^[^#;]")
/usr/local/bin/rr serve