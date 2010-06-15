cd ..
./tokenizeur.php -r -d ./tests/auditeur/scripts/ -g mysql,cache -I testsunitaires
cd auditeur
php auditeur.php tu
php lecture_module.php -I testsunitaires -a appelsfonctions -f ./tests/auditeur/scripts/appelsfonctions.php

