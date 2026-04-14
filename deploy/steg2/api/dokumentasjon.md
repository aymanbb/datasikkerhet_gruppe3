## API-dokumentasjon

Man har muligheten til å utføre operasjoner/funksjoner på nettsiden gjennom å gjøre
POST requests mot api_request_handler.php. 

Det fungerer som et indirekte funksjonskall hvor man sender autoriserende information og legger ved parametere og navnet på funksjonen man vil kalle i json format.

Man har muligheten til å logge seg inn eller registrere en student-bruker uten å allerede ha autentisert seg, naturligvis, men alle andre funksjoner behøver autorisering/login før bruk.

## Eksempler:
### Bash med curl

Kanskje den mest "straight forward" måten å gjøre post requests til api-et.

Autorisering:
```bash
curl -X POST http://158.39.188.219/api/api_request_handler.php \
  -H "Content-Type: application/json" \
  -c cookies.txt \
  -d '{"action":"login","username":"<username>", "password":"<password>}'
```

Request:
```bash
curl -X POST http://158.39.188.219/api/api_request_handler.php \
  -H "Content-Type: application/json" \
  -b cookies.txt \
  -d '{
        "action": "user_student_register",
        "username": "name nameson",
        "email": "email@email.com",
        "password": "hunter2"
      }'
```

### Powershell med Invoke-RestMethod

Login:
```powershell
# Definer en header variabel med nødvendig informasjon for å kunne gjøre en "request"
$headers = @{
     "Content-Type" = "application/json"
 }  # No backtick here

# Send request med headers
Invoke-RestMethod `
   -Uri "http://158.39.188.219/steg1/api/api_request_handler.php" `
   -Method POST `
   -Headers $headers `
   -Body '{"action":"login", "username":"<username>", "password":"<password>}' `
   -ContentType "application/json"
```
```powershell
# Definer en header variabel med nødvendig informasjon for å kunne gjøre en "request".
# "Authorization" må inneholde en session key man får tilbake etter å autorisere seg, ellers
# kan ikke handlingen utføres. 
$headers = @{
     "Content-Type" = "application/json"
     "Authorization" = "session <session id>"
 }  # No backtick here

Invoke-RestMethod `
   -Uri "http://158.39.188.219/steg1/api/api_request_handler.php" `
   -Method POST `
   -Headers $headers `
   -Body '{"action" : "subjects_fetch_all"}' `
   -ContentType "application/json"
```

## Eksponerte funksjoner
liste over funksjoner og parametere:
- *login* : params = (string \<username>, string \<password>) : returns: session
    - autentiserer mot et brukernavn og passord. Returnerer en session.

- *subjects_fetch_all*: params = ( none ) returns : array
    - Henter alle emner.
- *subject_message_submit*: params = ( int \<user_id>, int \<subject_pin>, string \<message_body> ) 
    - legger inn en melding til et emne basert fra en bruker. 

- *subject_message_answer_submit*: params = ( int \<message_id>, string \<answer_text> ) 
    - legger inn et svar fra en foreleser til en melding. 

- *subject_message_fetch_all*: params = ( string \<subject_pin> ) returns: array
    - henter alle meldinger assosiert med et emne

- *subject_message_comment_fetch_all*: params = ( string \<message_id> ) returns: array
    - henter alle kommentarer under en melding

- *user_student_register*: params = ( \<username>, \<email>, \<password> ) returns: bool
    - registrerer en student-bruker.
<!-- 
- user_lecturer_register: params = ( <username>, <email>, <password>, <image>, <subject>, <pin> ) returns: bool
    -
- subject_pin_exists: params = ( int <subject_pin> ) returns: bool
    - returns true if pin exists.
- user_find_by_username: params = ( string <username> ) returns: dictionary
    - 
-->
