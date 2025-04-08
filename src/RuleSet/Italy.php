<?php declare(strict_types=1);

namespace Libtelex\Telex2\RuleSet;

use Libtelex\Telex2\RuleSetInterface;
use Libtelex\Telex2\TelephoneNumber;
use Libtelex\Telex2\Utils;

use function array_keys;
use function array_unique;
use function implode;
use function range;
use function strlen;

use const false;
use const null;
use const true;

/**
 * See https://en.wikipedia.org/wiki/Telephone_numbers_in_Italy
 */
final class Italy implements RuleSetInterface
{
    /**
     * Complete area codes.  2-4 digits.
     *
     * Initial source: https://countrycode.org/italy
     *
     * @var array<string,string>
     */
    private const AREA_CODES = [
        '0144' => 'Acqui Terme',
        '0426' => 'Adria',
        '0922' => 'Agrigento',
        '0173' => 'Alba',
        '0182' => 'Albenga',
        '0924' => 'Alcamo',
        '0131' => 'Alessandria',
        '0883' => 'Andria',
        '0165' => 'Aosta',
        '0575' => 'Arezzo',
        '0322' => 'Arona',
        '0736' => 'Ascoli Piceno',
        '0141' => 'Asti',
        '0825' => 'Avellino',
        '0863' => 'Avezzano',
        '0424' => 'Bassano Del Grappa',
        '0828' => 'Battipaglia',
        '0323' => 'Baveno',
        '0437' => 'Belluno',
        '0824' => 'Benevento',
        '0471' => 'Bolzano',
        '0163' => 'Borgosesia',
        '0364' => 'Breno',
        '0472' => 'Bressanone',
        '0831' => 'Brindisi',
        '0474' => 'Brunico',
        '0331' => 'Busto Arsizio',
        '0933' => 'Caltagirone',
        '0934' => 'Caltanisetta',
        '0737' => 'Camerino',
        '0874' => 'Campobasso',
        '0142' => 'Casale Monferrato',
        '0375' => 'Casalmaggiore',
        '0823' => 'Caserta',
        '0776' => 'Cassino',
        '0981' => 'Castrovillari',
        '0961' => 'Catanzaro',
        '0462' => 'Cavalese',
        '0921' => 'Cefalu',
        '0885' => 'Cerignola',
        '0431' => 'Cervignano Del Friuli',
        '0547' => 'Cesena',
        '0578' => 'Chianciano Terme',
        '0343' => 'Chiavenna',
        '0871' => 'Chieti',
        '0766' => 'Civitavecchia',
        '0463' => 'Cles',
        '0346' => 'Clusone',
        '0377' => 'Codogno',
        '0533' => 'Comacchio',
        '0438' => 'Conegliano',
        '0436' => "Cortina d'Ampezzo",
        '0984' => 'Cosenza',
        '0373' => 'Crema',
        '0372' => 'Cremona',
        '0962' => 'Crotone',
        '0171' => 'Cuneo',
        '0324' => 'Domodossola',
        '0571' => 'Empoli',
        '0935' => 'Enna',
        '0429' => 'Este',
        '0732' => 'Fabriano',
        '0546' => 'Faenza',
        '0439' => 'Feltre',
        '0734' => 'Fermo',
        '0532' => 'Ferrara',
        '0524' => 'Fidenza',
        '0881' => 'Foggia',
        '0742' => 'Foligno',
        '0566' => 'Follonica',
        '0543' => 'Forli',
        '0771' => 'Formia',
        '0525' => 'Fornovo Di Taro',
        '0775' => 'Frosinone',
        '0833' => 'Gallipoli',
        '0481' => 'Gorizia',
        '0564' => 'Grosseto',
        '0781' => 'Iglesias',
        '0542' => 'Imola',
        '0183' => 'Imperia',
        '0865' => 'Isernia',
        '0125' => 'Ivrea',
        '0731' => 'Jesi',
        '0862' => "L'aquila",
        '0187' => 'La Spezia',
        '0973' => 'Lagonegro',
        '0968' => 'Lamezia Terme',
        '0872' => 'Lanciano',
        '0782' => 'Lanusei',
        '0123' => 'Lanzo Torinese',
        '0773' => 'Latina',
        '0832' => 'Lecce',
        '0341' => 'Lecco',
        '0442' => 'Legnano',
        '0586' => 'Livorno',
        '0964' => 'Locri',
        '0371' => 'Lodi',
        '0583' => 'Lucca',
        '0545' => 'Lugo',
        '0733' => 'Macerata',
        '0785' => 'Macomer',
        '0465' => 'Madonna de Campiglio, Tione Di Trento',
        '0836' => 'Maglie',
        '0884' => 'Manfredonia',
        '0376' => 'Mantova',
        '0585' => 'Massa',
        '0835' => 'Matera',
        '0972' => 'Melfi',
        '0344' => 'Menaggio',
        '0473' => 'Merano',
        '0535' => 'Mirandola',
        '0174' => 'Mondovi',
        '0423' => 'Montebelluna',
        '0572' => 'Montecatini Terme',
        '0384' => 'Mortara',
        '0976' => 'Muro Lucano',
        '0321' => 'Novara',
        '0143' => 'Novi Ligure',
        '0784' => 'Nuoro',
        '0789' => 'Olbia',
        '0783' => 'Oristano',
        '0763' => 'Orvieto',
        '0386' => 'Ostiglia',
        '0966' => 'Palmi',
        '0982' => 'Paola',
        '0521' => 'Parma',
        '0941' => 'Patti',
        '0382' => 'Pavia',
        '0721' => 'Pesaro',
        '0523' => 'Piacenza',
        '0435' => 'Pieve di adore',
        '0121' => 'Pinerolo',
        '0565' => 'Piombino',
        '0573' => 'Pistoia',
        '0765' => 'Poggio Mirteto',
        '0587' => 'Pontedera',
        '0434' => 'Pordenone',
        '0534' => 'Porretta Terme',
        '0971' => 'Potenza',
        '0574' => 'Prato',
        '0932' => 'Ragusa',
        '0185' => 'Rapallo',
        '0544' => 'Ravenna',
        '0965' => 'Reggio Calabria',
        '0522' => 'Reggio Emilia',
        '0746' => 'Rieti',
        '0541' => 'Rimini',
        '0124' => 'Rivarolo Canavese',
        '0983' => 'Rossano',
        '0464' => 'Rovereto',
        '0425' => 'Rovigo',
        '0827' => 'S. Angelo Dei Lombardi',
        '0735' => 'S. Benedetto Del Tronto',
        '0421' => 'S. Dona Di Piave',
        '0345' => 'S. Pellegrino Terme',
        '0166' => 'Saint Vincent',
        '0975' => 'Sala Consilina',
        '0365' => 'Salo',
        '0175' => 'Saluzzo',
        '0549' => 'San Marino',
        '0184' => 'San Remo',
        '0882' => 'San Severo',
        '0536' => 'Sassuolo',
        '0172' => 'Savigliano',
        '0985' => 'Scalea',
        '0445' => 'Schio',
        '0925' => 'Sciacca',
        '0362' => 'Seregno',
        '0577' => 'Siena',
        '0931' => 'Siracusa',
        '0342' => 'Sondrio',
        '0374' => 'Soresina',
        '0967' => 'Soverato',
        '0427' => 'Spilimbergo',
        '0743' => 'Spoleto',
        '0385' => 'Stradella',
        '0864' => 'Sulmona',
        '0122' => 'Susa',
        '0942' => 'Taormina',
        '0428' => 'Tarvisio',
        '0861' => 'Teramo',
        '0875' => 'Termoli',
        '0744' => 'Terni',
        '0774' => 'Tivoli',
        '0433' => 'Tolmezzo',
        '0923' => 'Trapani',
        '0461' => 'Trento',
        '0363' => 'Treviglio',
        '0422' => 'Treviso',
        '0432' => 'Udine',
        '0722' => 'Urbino',
        '0332' => 'Varese',
        '0873' => 'Vasto',
        '0161' => 'Vercelli',
        '0584' => 'Viareggio',
        '0963' => 'Vibo Valentia',
        '0444' => 'Vicenza',
        '0381' => 'Vigevano',
        '0974' => 'Villa Della Lucania',
        '0761' => 'Viterbo',
        '0383' => 'Voghera',
        '0588' => 'Volterra',
        '071' => 'Ancona',
        '080' => 'Bari',
        '035' => 'Bergamo',
        '015' => 'Biella',
        '051' => 'Bologna',
        '030' => 'Brescia',
        '070' => 'Cagliari',
        '081' => 'Capri, Napoli (Naples)',
        '095' => 'Catania',
        '031' => 'Como',
        '055' => 'Firenze (Florence)',
        '010' => 'Genoa',
        '090' => 'Messina',
        '059' => 'Modena',
        '039' => 'Monza',
        '049' => 'Padova',
        '091' => 'Palermo',
        '075' => 'Perugia',
        '085' => 'Pescara',
        '050' => 'Pisa',
        '089' => 'Salerno',
        '079' => 'Sassari',
        '019' => 'Savona',
        '099' => 'Taranto',
        '011' => 'Torino',
        '040' => 'Trieste',
        '041' => 'Venezia (Venice)',
        '045' => 'Verona',
        '02' => 'Milan (Milano)',
        '06' => 'Rome, Vatican City',
    ];

    /**
     * See:
     * - https://www.justlanded.co.uk/english/Italy/Articles/Telephone-Internet/Prefixes
     * - https://krispcall.com/blog/italy-phone-number-format/
     */
    private const array MOBILE_CARRIER_CODES = [
        'TIM' => '330|331|333|334|335|336|337|338|339|360|363|366|368',
            'CoopVoce (TIM)' => '3311',
            'MTV Mobile (TIM)' => '366|331',
            'Noverca (TIM)' => '3707',
            'Tiscali Mobile (TIM)' => '3701',
        'Vodafone Italia' => '340|342|345|346|347|348|349',
            'BT Mobile (Vodafone)' => '3777',
            'Daily Telecom Mobile (Vodafone)' => '3778',
            'ERG Mobile (Vodafone)' => '3775',
            'PosteMobile (Vodafone)' => '3771|3772',
            'UNOMobile (Vodafone)' => '3773',
        'Wind' => '320|323|327|328|329|380|383|388|389',
            // 'A-Mobile (Wind)' => '389',
        '3 Italia' => '390|391|392|393',
            'Fastweb (3 Italia)' => '373',
        'ILIAD' => '3515|3516|3517|3518|3519|3520',
    ];

    /**
     * @override
     */
    public function getCountryCallingCode(): string
    {
        return '39';
    }

    /**
     * @override
     */
    public function getTrunkCode(): string
    {
        return '';
    }

    /**
     * @override
     * @return int[]
     */
    public function getNationalNumberLength(): array
    {
        return array_unique([
            ...range(6, 11),  // Fixed-line
            9, 10,  // Mobile
        ]);
    }

    /**
     * See https://en.wikipedia.org/wiki/National_conventions_for_writing_telephone_numbers#Italy
     *
     * Examples:
     * - [Italian Government](https://www.governo.it/en): "(+39) 06.6779.1"
     * - [Ministry of Tourism](https://www.ministeroturismo.gov.it/): "06.164169910"
     * - [Communications Regulatory Authority](https://www.agcom.it/contatti-telefonici-e-posta-elettronica): "081.7507111" and "06.69644111"
     * - [Shell](https://www.shell.it/about-us/per-contattarci.html): "+39 02 00695000"
     * - [GSK]: "+39 045 57741111"
     *
     * @override
     */
    public function formatIntl(TelephoneNumber $telephoneNumber): bool
    {
        // (By this point, the national number should contain only digits and be the correct length)
        $nationalNumber = $telephoneNumber->getNationalNumber();

        $prefix = '';

        $telephoneNumberType = match (true) {
            Utils::startsWith(array_keys(self::AREA_CODES), $nationalNumber, $prefix)
                => TelephoneNumber::TYPE_FIXED_LINE,

            Utils::startsWith(self::MOBILE_CARRIER_CODES, $nationalNumber, $prefix)
                => TelephoneNumber::TYPE_MOBILE,

            default
                => null,
        };

        if (!$telephoneNumberType) {
            $telephoneNumber->noMatchForCountry();

            return false;
        }

        $countryCallingCode = $this->getCountryCallingCode();
        $numberClusters = Utils::chunk($nationalNumber, patternOrLength: [strlen($prefix), /* (rest) */]);

        $telephoneNumber->matchForCountry(
            $countryCallingCode,
            $telephoneNumberType,
            "+{$countryCallingCode} " . implode(' ', $numberClusters),
        );

        return true;
    }
}
