#! /bin/bash

bin/cake migrations migrate -p CakeDC/Users
bin/cake migrations migrate
bin/cake cache clear_all

bin/cake migrations seed --seed CreateUsersSeed
