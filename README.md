# Drupal.hu karbantarto kezikonyv

## Build status

Stabil ag: [![Build Status](https://travis-ci.org/drupalhu/drupal.hu.svg?branch=stable-acquia)](https://travis-ci.org/drupalhu/drupal.hu)


## Hogyan fejlesszek a drupal.hu kodjan?

### Uj kontributor beallitasa

1. [Forkold](http://help.github.com/fork-a-repo/) a hivatalos repot: https://github.com/drupalhu/drupal.hu

2. Klonozd a projectet a gepedre (a USERNAME a te githubos userneved)

	```
	$ git clone git@github.com:USERNAME/drupal.hu.git
	```

3. Add hozza a hivatalos repot remote-kent, a neve legyen "upstream".

	```
	$ cd drupal.hu
	$ git remote add upstream git://github.com/drupalhu/drupal.hu.git
	```

4. Keszits egy uj branchot a munkadhoz

	```
	$ git checkout -b nagyonmeno-feature
	```

5. Fejlessz az uj branchedben

6. Minden valtoztatast committolj ebbe az uj branchbe

	```
	$ git add .
	$ git commit -m "Use english commit messages and use full sentences that describe the change."
	```

7. Pushold a kodot a githubon levo forkodba.

	```
	$ git push origin nagyonmeno-feature
	```

8. 5-7 lepeseket ismeteld, ameddig keszen nincs amin dolgoztal.

9. Huzd le az upstream-rol a tobbiek esetleges valtoztatasait (ha kozben masok is dolgoznak a projecten, akkor a hivatalos repo tartalma megvaltozhat).

	```
	$ git fetch upstream
	```

10. Frissitsed a helyi stabil kodot.

	```
	$ git checkout stable-acquia
	$ git pull upstream stable-acquia
	```

11. Rebase-ld a te branchedben levo valtoztatasokat az upstream acquia-stable aganak tetejere

	```
	$ git checkout nagyonmeno-feature
	$ git rebase stable-acquia
	```

12. Rebase kozben conflictok konnyen bekovetkezhetnek. Ezek feloldasa utan ``git add .`` frissiti az indexet, es folytatni lehet a rebase-t:

	```
	$ git rebase --continue
	```

13. Pushold a kododat a sajat origin repodba. Miutan a rebase atpakolja a committokat es gyakorlatilag ujakat hoz letre, ezert valoszinuleg --force-al lehet majd csak pusholni.

    ```
    $ git push --force origin nagyonmeno-feature
    ```

14. Keszits egy Pull Requestet a hivatalos repo oldalan a forkodban talalhato uj branchedre, hogy a karbantartok beolvaszthassak a kodot.

### Tovabbi kontribociok menete

1. Mielott megkezded a munkat huzd le az upstream valtoztatasait, es rebaseld a sajat repodat. Ezaltal nem kell felesleges merge committokkal szennyezni a sajat logodat.
2. Valassz ki egy branchot amin dolgozni akarsz
3. Implementald a valtoztatasokat, valtoztasd a kodot
4. Committold a helyi, sajat repository-dba, es pushold a sajat githubos forkodba.
5. Keszits egy Pull Requestet a hivatalos repo oldalan
