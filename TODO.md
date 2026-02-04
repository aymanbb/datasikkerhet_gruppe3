# TODO
## Høy prioritet

- [ ] subject_messages.php 
    - [x] teste med å vise alle meldinger 
    - [ ] Sende inn melding i et emne
    - [ ] sortere viste meldinger på emne.

- [ ] Guest login page
    - [x] Form med emne-pin og submit knapp.
        > sjekker om emne-pin eksisterer. ingen redirect
    - [ ] redirect til emneside med pinkode som parameter?
        > parameter, eller en annen måte å gjøre det på?
        > finner emne etter parameter og fyller ut siden 

- [ ] index.php
    - [ ] faktisk kunne logge inn

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

## Mid prioritet
- [ ] navigasjon frem og tilbake på sider
- [ ] skjule ip og filnavn i url-bar
- [ ] inputvalidering
- [ ] Rydde på server
- [ ] refaktorere...
- [ ] rate limiting på alle requests