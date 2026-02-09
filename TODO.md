# TODO
## Høy prioritet

- [ ] subject_messages.php 
    - [x] teste med å vise alle meldinger 
    - [x] Sende inn melding i et emne
    - [x] emneside. Viser meldinger som hører til emnet basert på url parameter.

- [ ] Guest login page
    - [x] Form med emne-pin og submit knapp.
        > sjekker om emne-pin eksisterer. ingen redirect
    - [x] redirect til emneside med pinkode som parameter?
        > parameter, eller en annen måte å gjøre det på?
        > finner emne etter parameter og fyller ut siden 
        - [x] implementert
        - [ ] testet

- [ ] index.php
    - [ ] faktisk kunne logge inn
        - [ ] redirect til emneloversikt.php om du er student
        - [ ] redirect til subject_messages.php om du er foreleser

- [ ] foreleser_register.php
    - [ ] lage en sjekk i foreleser_register.php som sjekker om emnenavn allerede eksisterer.
        > må håndheve at det må være unikt

- [ ] koble alle php-filan opp mot den test-databasen til aaro
- [ ] glemt passord feature
    > sende epost?

- [ ] side med liste over emner
- [ ] koble melding-side til emne dynamisk

- [ ] Laste opp bilde til server
    - [ ] auto-downscale/resize
    - [ ] whitelist .png/jpg
    - [ ] fremvisning av bilde på emneside

- [ ] implementere session-sjekk overalt

- [ ] Database:
    - [ ] migrer til reell database
    - [ ] ordne backup
    - [ ] Migrere til stored procedures(?)
    
 - [ ] koble opp bilde-mottakelse i php-fila til "registrer foreleser", akkurat nå blir det feltet ignorert

## Mid prioritet
- [ ] navigasjon frem og tilbake på sider
- [ ] skjule ip og filnavn i url-bar
- [ ] inputvalidering
- [ ] Rydde på server
- [ ] refaktorere...
- [ ] rate limiting på alle requests
- [ ] Sikre databasetilkoblingen, 
    > flytte ut i config-fil antakelig
- [ ] Slette test-bruker i database
- [ ] sikre brukertilganger generelt!

- [ ] integrere en form for logging?