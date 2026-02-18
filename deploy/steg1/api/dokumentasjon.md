## POST basert API

Man har muligheten til å utføre operasjoner/funksjoner på nettsiden gjennom å gjøre
POST requests mot api_request_handler.php. 

Det fungerer som et indirekte funksjonskall i JSON format som er strukturert slik:
- string : "session_id" : som inneholder session_id man får etter autentisering. **Må** inneholde dette, med mindre man forsøker å logge inn.
- string : "action" : navnet på funksjonen man kaller.
- varierende type : parametere som brukes i funksjonen.

Her er et eksempel i curl:

```bash
curl -X POST http://158.39.188.219/api/api_request_handler.php \
  -H "Content-Type: application/json" \
  -d '{
        "session_id": "abc123",
        "action": "user_student_register",
        "username": "name nameson",
        "email": "email@email.com",
        "password": "hunter2"
      }'
```

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
