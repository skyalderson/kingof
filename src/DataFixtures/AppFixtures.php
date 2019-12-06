<?php

namespace App\DataFixtures;

use App\Entity\Board;
use App\Entity\Box;
use App\Entity\BoxType;
use App\Entity\Game;
use App\Entity\Mode;
use App\Entity\Monster;
use App\Entity\Rule;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function remove_accent($str)
    {
        $a = ['À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð',
                    'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã',
                    'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ',
                    'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ',
                    'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę',
                    'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī',
                    'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ',
                    'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ',
                    'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť',
                    'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ',
                    'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ',
                    'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ', ];

        $b = ['A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O',
                    'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c',
                    'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u',
                    'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D',
                    'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g',
                    'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K',
                    'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o',
                    'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S',
                    's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W',
                    'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i',
                    'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o', ];

        return str_replace($a, $b, $str);
    }

    /* Générateur de Slug (Friendly Url) : convertit un titre en une URL conviviale.*/
    public function slug($str)
    {
        return mb_strtolower(preg_replace(['/[^a-zA-Z0-9 \'-]/', '/[ -\']+/', '/^-|-$/'],
        ['', '-', ''], $this->remove_accent($str)));
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        //BoxTypes
        $boxtype[1] = new BoxType();
        $boxtype[1]->setName('King of Tokyo');
        $manager->persist($boxtype[1]);

        $boxtype[2] = new BoxType();
        $boxtype[2]->setName('King of New-York');
        $manager->persist($boxtype[2]);

        $boxtype[3] = new BoxType();
        $boxtype[3]->setName('Extensions');
        $manager->persist($boxtype[3]);

        $boxtype[4] = new BoxType();
        $boxtype[4]->setName('Monstres Bonus');
        $manager->persist($boxtype[4]);

        // Boxes
        $box[1] = new Box();
        $box[1]->setName('King of Tokyo - 1ère Édition');
        $box[1]->setBoxType($boxtype[1]);
        $manager->persist($box[1]);

        $box[2] = new Box();
        $box[2]->setName('King of Tokyo - 2ème Édition');
        $box[2]->setBoxType($boxtype[1]);
        $manager->persist($box[2]);

        $box[3] = new Box();
        $box[3]->setName('Power Up! King of Tokyo');
        $box[3]->setBoxType($boxtype[3]);
        $manager->persist($box[3]);

        $box[4] = new Box();
        $box[4]->setName('Halloween collector pack');
        $box[4]->setBoxType($boxtype[3]);
        $manager->persist($box[4]);

        $box[5] = new Box();
        $box[5]->setName('Monster Pack 1: Cthulhu');
        $box[5]->setBoxType($boxtype[3]);
        $manager->persist($box[5]);

        $box[6] = new Box();
        $box[6]->setName('Monster Pack 2: King Kong');
        $box[6]->setBoxType($boxtype[3]);
        $manager->persist($box[6]);

        $box[7] = new Box();
        $box[7]->setName('Monster Pack 3: Anubis');
        $box[7]->setBoxType($boxtype[3]);
        $manager->persist($box[7]);

        $box[8] = new Box();
        $box[8]->setName('Monster Pack 4: Cybertooth');
        $box[8]->setBoxType($boxtype[3]);
        $manager->persist($box[8]);

        $box[9] = new Box();
        $box[9]->setName('King of New-York');
        $box[9]->setBoxType($boxtype[2]);
        $manager->persist($box[9]);

        $box[10] = new Box();
        $box[10]->setName('Power Up! King of New-York');
        $box[10]->setBoxType($boxtype[3]);
        $manager->persist($box[10]);

        $box[11] = new Box();
        $box[11]->setName('Monstres Bonus');
        $box[11]->setBoxType($boxtype[4]);
        $manager->persist($box[11]);

        // Modes (1: Casual, 2: Tournament)
        $mode[1] = new Mode();
        $mode[1]->setName('Classique');
        $manager->persist($mode[1]);

        $mode[2] = new Mode();
        $mode[2]->setName('Tournoi');
        $manager->persist($mode[2]);

        // Jeux
        $board[1] = new Board();
        $board[1]->setName('King of Tokyo');
        $board[1]->setAvailable(1);
        $manager->persist($board[1]);

        $board[2] = new Board();
        $board[2]->setName('King of New-York');
        $board[2]->setAvailable(1);
        $manager->persist($board[2]);

        // Règles additionnelles
        $rule[1] = new Rule();
        $rule[1]->setName('Cartes Évolution');
        $rule[1]->addApplicableToBoard($board[1]);
        $rule[1]->addApplicableToBoard($board[2]);
        $rule[1]->setAvailable(1);
        $manager->persist($rule[1]);

        $rule[2] = new Rule();
        $rule[2]->setName('Tuiles Cultiste');
        $rule[2]->addApplicableToBoard($board[1]);
        $rule[2]->setAvailable(1);
        $manager->persist($rule[2]);

        $rule[3] = new Rule();
        $rule[3]->setName('Tuiles Cultiste / Temple de Cthulhu');
        $rule[3]->addApplicableToBoard($board[2]);
        $rule[3]->setAvailable(1);
        $manager->persist($rule[3]);

        $rule[4] = new Rule();
        $rule[4]->setName('Tour de Tokyo');
        $rule[4]->addApplicableToBoard($board[1]);
        $rule[4]->setAvailable(1);
        $manager->persist($rule[4]);

        $rule[5] = new Rule();
        $rule[5]->setName('Empire State Building');
        $rule[5]->addApplicableToBoard($board[2]);
        $rule[5]->setAvailable(1);
        $manager->persist($rule[5]);

        $rule[6] = new Rule();
        $rule[6]->setName('Carte Belle');
        $rule[6]->addApplicableToBoard($board[1]);
        $rule[6]->addApplicableToBoard($board[2]);
        $rule[6]->setAvailable(0);
        $manager->persist($rule[6]);

        $rule[7] = new Rule();
        $rule[7]->setName('Dé du Destin & Cartes Malédiction');
        $rule[7]->addApplicableToBoard($board[1]);
        $rule[7]->addApplicableToBoard($board[2]);
        $rule[7]->setAvailable(0);
        $manager->persist($rule[7]);

        $rule[8] = new Rule();
        $rule[8]->setName('Mode Berserk');
        $rule[8]->addApplicableToBoard($board[1]);
        $rule[8]->addApplicableToBoard($board[2]);
        $rule[8]->setAvailable(0);
        $manager->persist($rule[8]);

        $rule[9] = new Rule();
        $rule[9]->setName('Évolutions Mutantes');
        $rule[9]->addApplicableToBoard($board[1]);
        $rule[9]->addApplicableToBoard($board[2]);
        $rule[9]->setAvailable(0);
        $manager->persist($rule[9]);

        //for ($i = 0 ; $i <= 9 $i++)
        //{
        //$rule[$i]->set

        //}

        // Monstres KoT 1ère édition
        $_monsters['Alienoid'] = 1;
        $_monsters['Cyber Bunny'] = 1;
        $_monsters['Giga Zaur'] = 1;
        $_monsters['Kraken'] = 1;
        $_monsters['Meka Dragon'] = 1;
        $_monsters['The King'] = 1;

        // Monstres remplaçants KoT 2ème édition
        $_monsters['Cyber Kitty'] = 2;
        $_monsters['Space Penguin'] = 2;

        //Monstres King of New-York
        $_monsters['Captain Fish'] = 9;
        $_monsters['Drakonis'] = 9;
        $_monsters['Kong'] = 9;
        $_monsters['Mantis'] = 9;
        $_monsters['Rob'] = 9;
        $_monsters['Sheriff'] = 9;

        // Monstres Expansions
        $_monsters['Pandakai'] = 3;
        $_monsters['Pumpkin Jack'] = 4;
        $_monsters['Boogie Woogie'] = 4;
        $_monsters['Cthulhu'] = 5;
        $_monsters['King Kong'] = 6;
        $_monsters['Anubis'] = 7;
        $_monsters['Cybertooth'] = 8;
        $_monsters['Mega Shark'] = 10;

        // Monstres Bonus
        $_monsters['Ali-San'] = 11;
        $_monsters['Baby Gigazaur'] = 11;
        $_monsters['Brockenbar'] = 11;
        $_monsters['Crabomination'] = 11;
        $_monsters['Draccus'] = 11;
        $_monsters['Iron Rook'] = 11;
        $_monsters['Kookie'] = 11;
        $_monsters['Lollybot'] = 11;
        $_monsters['Orange Death'] = 11;
        $_monsters['Pouic'] = 11;
        $_monsters['Rozy Pony'] = 11;
        $_monsters['X-smash Tree'] = 11;
        $_monsters['Zombie Alpha'] = 11;

        $i = 1;
        foreach ($_monsters as $name => $m) {
            $monster[$i] = new Monster();
            $monster[$i]->setName($name);
            $monster[$i]->setBox($box[$m]);
            $slug_name = $this->slug($name);
            $monster[$i]->setImgName("$slug_name.png");
            $manager->persist($monster[$i]);
            ++$i;
        }

        // Users
        for ($i = 0; $i < 20; ++$i) {
            $username = $faker->userName;
            $user[$i] = new User();
            $user[$i]->setUsername($username);
            $user[$i]->setPassword($this->encoder->encodePassword($user[$i], 'pwdpwd'));
            $user[$i]->setRoles(['ROLE_USER']);
            $manager->persist($user[$i]);
        }

        /*
        $nb_games = 12;
        for($i=1; $i<=$nb_games; $i++)
        {
            $game = new Game();

            $idGame = (string) $i;

            $game->setName('Partie #'.$idGame);
            $game->setCreationDate($faker->datetime());
            $state = $i%3 + 1;
            $game->setState($state);

            $id_user = rand(0, 9);
            $game->setCreator($user[$id_user]);

            $game->setBoard($board[$b]);
            $game->setGamemode($gamemode[$mode]);
            $game->setVersion($version[]);

            $manager->persist($game);
        }*/
        $manager->flush();
    }
}
