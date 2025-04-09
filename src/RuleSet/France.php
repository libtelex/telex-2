<?php declare(strict_types=1);

namespace Libtelex\Telex2\RuleSet;

use Libtelex\Telex2\RuleSetInterface;
use Libtelex\Telex2\StringObject;
use Libtelex\Telex2\TelephoneNumber;
use Libtelex\Telex2\Utils;

use function array_keys;
use function implode;
use function str_split;

use const false;
use const null;
use const true;

/**
 * See https://en.wikipedia.org/wiki/Telephone_numbers_in_France
 */
final class France implements RuleSetInterface
{
    /**
     * Complete area codes.  4 digits, includes the trunk code.
     *
     * Initial source: https://en.wikipedia.org/wiki/Local_telephone_area_codes_in_France#List_of_departmental_area_codes_by_geographic_location
     *
     * @var array<string,string>
     */
    private const AREA_CODES = [
        '0130' => "Yvelines, Val-d'Oise",
        '0134' => "Yvelines, Val-d'Oise",
        '0139' => "Yvelines, Val-d'Oise",
        '0140' => 'Paris',
        '0141' => "Hauts-de-Seine, Val-d'Oise",
        '0142' => "Paris, Hauts-de-Seine, Seine-Saint-Denis, Val-de-Marne",
        '0143' => "Paris, Seine-Saint-Denis, Val-de-Marne",
        '0144' => 'Paris',
        '0145' => "Paris, Val-de-Marne",
        '0146' => "Paris, Essonne, Hauts-de-Seine, Val-de-Marne",
        '0147' => "Paris, Hauts-de-Seine, Val-de-Marne",
        '0148' => "Paris, Seine-Saint-Denis, Val-de-Marne, Val-d'Oise",
        '0149' => "Paris, Hauts-de-Seine, Val-de-Marne",
        '0153' => 'Paris',
        '0155' => "Paris, Hauts-de-Seine",
        '0156' => "Paris, Val-de-Marne",
        '0158' => 'Paris',
        '0160' => "Seine-et-Marne, Essonne",
        '0162' => 'Telemarketing',
        '0163' => 'Telemarketing',
        '0164' => "Seine-et-Marne, Essonne",
        '0169' => "Essonne, Seine-et-Marne",
        '0170' => 'Paris',
        '0172' => "Paris, Seine-Saint-Denis",
        '0173' => 'Paris',
        '0174' => 'Paris',
        '0175' => 'Paris',
        '0176' => "Paris, Hauts-de-Seine",
        '0177' => 'Paris',
        '0178' => "Paris, Hauts-de-Seine",
        '0179' => 'Paris',
        '0180' => 'Paris',
        '0181' => 'Paris',
        '0182' => 'Paris',
        '0183' => 'Paris',
        '0184' => 'Paris',
        '0188' => "Paris, Seine-et-Marne, Yvelines, Essonne, Hauts-de-Seine, Seine-Saint-Denis, Val-de-Marne, Val-d'Oise",
        '0214' => "Calvados, Manche, Orne",
        '0218' => "Cher, Eure-et-Loir, Indre, Indre-et-Loire, Loir-et-Cher, Loiret",
        '0219' => "Cher, Eure-et-Loir, Indre, Indre-et-Loire, Loir-et-Cher, Loiret",
        '0221' => "Côtes-d'Armor, Finistère, Ille-et-Vilaine, Morbihan",
        '0222' => "Côtes-d'Armor, Finistère, Ille-et-Vilaine, Morbihan",
        '0223' => 'Ille-et-Vilaine',
        '0228' => "Loire-Atlantique, Vendée",
        '0229' => 'Finistère',
        '0230' => "Côtes-d'Armor, Finistère, Ille-et-Vilaine, Morbihan",
        '0231' => 'Calvados',
        '0232' => 'Eure',
        '0233' => "Manche, Orne",
        '0234' => "Cher, Eure-et-Loir, Indre, Indre-et-Loire, Loir-et-Cher, Loiret",
        '0235' => 'Seine-Maritime',
        '0236' => "Cher, Eure-et-Loir, Indre, Indre-et-Loire, Loir-et-Cher, Loiret",
        '0237' => 'Eure-et-Loir',
        '0238' => 'Loiret',
        '0240' => 'Loire-Atlantique',
        '0241' => 'Maine-et-Loire',
        '0243' => "Mayenne, Sarthe",
        '0244' => "Loire-Atlantique, Maine-et-Loire, Mayenne, Sarthe, Vendée",
        '0245' => "Cher, Eure-et-Loir, Indre, Indre-et-Loire, Loir-et-Cher, Loiret",
        '0246' => "Cher, Eure-et-Loir, Indre, Indre-et-Loire, Loir-et-Cher, Loiret",
        '0247' => 'Indre-et-Loire',
        '0248' => 'Cher',
        '0249' => "Loire-Atlantique, Maine-et-Loire, Mayenne, Sarthe, Vendée",
        '0250' => "Calvados, Manche, Orne",
        '0251' => "Vendée, Loire-Atlantique",
        '0252' => "Loire-Atlantique, Maine-et-Loire, Mayenne, Sarthe, Vendée",
        '0253' => "Loire-Atlantique, Maine-et-Loire, Mayenne, Sarthe, Vendée",
        '0254' => "Indre, Loir-et-Cher",
        '0255' => "Loire-Atlantique, Maine-et-Loire, Mayenne, Sarthe, Vendée",
        '0256' => "Côtes-d'Armor, Finistère, Ille-et-Vilaine, Morbihan",
        '0257' => "Côtes-d'Armor, Finistère, Ille-et-Vilaine, Morbihan",
        '0258' => "Calvados, Manche, Orne",
        '0261' => "Calvados, Manche, Orne",
        '0262' => 'Réunion',
        '0263' => 'Réunion',
        '0269' => 'Mayotte',
        '0270' => 'Telemarketing',
        '0271' => 'Telemarketing',
        '0272' => "Loire-Atlantique, Maine-et-Loire, Mayenne, Sarthe, Vendée",
        '0276' => "Eure, Seine-Maritime",
        '0277' => "Eure, Seine-Maritime",
        '0278' => "Eure, Seine-Maritime",
        '0279' => "Eure, Seine-Maritime",
        '0285' => "Loire-Atlantique, Maine-et-Loire, Mayenne, Sarthe, Vendée",
        '0290' => "Côtes-d'Armor, Finistère, Ille-et-Vilaine, Morbihan",
        '0296' => "Côtes-d'Armor",
        '0297' => 'Morbihan',
        '0298' => 'Finistère',
        '0299' => 'Ille-et-Vilaine',
        '0310' => "Ardennes, Aube, Marne, Haute-Marne",
        '0320' => 'Central Nord',
        '0321' => 'Pas-de-Calais',
        '0322' => 'Somme',
        '0323' => 'Aisne',
        '0324' => 'Ardennes',
        '0325' => "Aube, Haute-Marne",
        '0326' => 'Marne',
        '0327' => 'South of Nord',
        '0328' => 'North of Nord',
        '0329' => "Meuse, Vosges",
        '0339' => "Doubs, Jura, Haute-Saône, Territoire de Belfort",
        '0344' => 'Oise',
        '0345' => "Côte-d'Or, Nièvre, Saône-et-Loire, Yonne",
        '0351' => "Ardennes, Aube, Marne, Haute-Marne",
        '0352' => "Ardennes, Aube, Marne, Haute-Marne",
        '0353' => "Ardennes, Aube, Marne, Haute-Marne",
        '0354' => "Meurthe-et-Moselle, Meuse, Moselle, Vosges",
        '0355' => "Meurthe-et-Moselle, Meuse, Moselle, Vosges",
        '0356' => "Meurthe-et-Moselle, Meuse, Moselle, Vosges",
        '0357' => "Meurthe-et-Moselle, Meuse, Moselle, Vosges",
        '0358' => "Côte-d'Or, Nièvre, Saône-et-Loire, Yonne",
        '0359' => "Nord, Pas-de-Calais",
        '0360' => "Aisne, Oise, Somme",
        '0361' => "Nord, Pas-de-Calais",
        '0362' => "Nord, Pas-de-Calais",
        '0363' => "Doubs, Jura, Haute-Saône, Territoire de Belfort",
        '0364' => "Aisne, Oise, Somme",
        '0365' => "Aisne, Oise, Somme",
        '0366' => "Nord, Pas-de-Calais",
        '0367' => "Bas-Rhin, Haut-Rhin",
        '0368' => "Bas-Rhin, Haut-Rhin",
        '0369' => "Bas-Rhin, Haut-Rhin",
        '0370' => "Doubs, Jura, Haute-Saône, Territoire de Belfort",
        '0371' => "Côte-d'Or, Nièvre, Saône-et-Loire, Yonne",
        '0372' => "Meurthe-et-Moselle, Meuse, Moselle, Vosges",
        '0373' => "Côte-d'Or, Nièvre, Saône-et-Loire, Yonne",
        '0375' => "Aisne, Oise, Somme",
        '0376' => "Nord, Pas-de-Calais",
        '0377' => 'Telemarketing',
        '0378' => 'Telemarketing',
        '0379' => "Côte-d'Or, Nièvre, Saône-et-Loire, Yonne",
        '0380' => "Côte-d'Or",
        '0381' => 'Doubs',
        '0382' => "North of Meurthe-et-Moselle, Moselle",
        '0383' => 'South of Meurthe-et-Moselle',
        '0384' => "Jura, Haute-Saône, Territoire de Belfort",
        '0385' => "North of l'Ain, Saône-et-Loire",
        '0386' => "Yonne, Nièvre",
        '0387' => 'Moselle',
        '0388' => 'Bas-Rhin',
        '0389' => 'Haut-Rhin',
        '0390' => "Bas-Rhin, Haut-Rhin",
        '0392' => 'Bas-Rhin',
        '0411' => "Aude, Gard, Hérault, Lozère, Pyrénées-Orientales",
        '0413' => "Alpes-de-Haute-Provence, Hautes-Alpes, Bouches-du-Rhône, Vaucluse",
        '0415' => "Allier, Cantal, Haute-Loire, Puy-de-Dôme",
        '0420' => "Corse-du-Sud, Haute-Corse",
        '0422' => "Alpes-Maritimes, Var",
        '0423' => "Alpes-Maritimes, Var",
        '0424' => 'Telemarketing',
        '0425' => 'Telemarketing',
        '0426' => "Ain, Allier, Ardèche, Drôme, Loire, Rhône",
        '0427' => "Ain, Ardèche, Drôme, Loire, Rhône",
        '0428' => "Lyon (RHONE)",
        '0430' => "Aude, Gard, Hérault, Lozère, Pyrénées-Orientales",
        '0432' => "Bouches-du-Rhône, Vaucluse",
        '0434' => "Aude, Gard, Hérault, Lozère, Pyrénées-Orientales",
        '0437' => 'Rhône',
        '0438' => 'Isère',
        '0442' => "Central and eastern Bouches-du-Rhône, except Marseille",
        '0443' => "Allier, Cantal, Haute-Loire, Puy-de-Dôme",
        '0444' => "Allier, Cantal, Haute-Loire, Puy-de-Dôme",
        '0448' => "Aude, Gard, Hérault, Lozère, Pyrénées-Orientales",
        '0450' => "Far northeast of l'Ain, Haute-Savoie",
        '0456' => "Isère, Savoie, Haute-Savoie",
        '0457' => "Isère, Savoie, Haute-Savoie",
        '0458' => "Isère, Savoie, Haute-Savoie",
        '0463' => "Allier, Cantal, Haute-Loire, Puy-de-Dôme",
        '0465' => "Alpes-de-Haute-Provence, Hautes-Alpes, Bouches-du-Rhône, Vaucluse",
        '0466' => "Gard, Lozère",
        '0467' => "Gard, Hérault",
        '0468' => "Aude, Pyrénées-Orientales",
        '0469' => "Ain, Ardèche, Drôme, Loire, Rhône",
        '0470' => 'Allier',
        '0471' => "Cantal, Haute-Loire",
        '0472' => 'Lyon',
        '0473' => 'Puy-de-Dôme',
        '0474' => "Ain, Rhône except Lyon, North of Isère",
        '0475' => "Ardèche, Drôme",
        '0476' => 'Isère',
        '0477' => 'Loire',
        '0478' => 'Lyon',
        '0479' => "Far east of l'Ain, Savoie",
        '0480' => "Isère, Savoie, Haute-Savoie",
        '0481' => "Ain, Ardèche, Drôme, Loire, Rhône",
        '0482' => "Ain, Ardèche, Drôme, Loire, Rhône",
        '0483' => "Alpes-Maritimes, Var",
        '0484' => "Alpes-de-Haute-Provence, Hautes-Alpes, Bouches-du-Rhône, Vaucluse",
        '0485' => "Isère, Savoie, Haute-Savoie",
        '0486' => "Alpes-de-Haute-Provence, Hautes-Alpes, Bouches-du-Rhône, Vaucluse",
        '0487' => "Ain, Ardèche, Drôme, Loire, Rhône",
        '0488' => "Alpes-de-Haute-Provence, Hautes-Alpes, Bouches-du-Rhône, Vaucluse",
        '0489' => "Alpes-Maritimes, Bouches-du-Rhône, Var",
        '0490' => "West of Bouches-du-Rhône, Vaucluse",
        '0491' => 'Marseille',
        '0492' => "Alpes-de-Haute-Provence, Hautes-Alpes, Alpes-Maritimes",
        '0493' => 'Alpes-Maritimes',
        '0494' => 'Var',
        '0495' => "Corse-du-Sud, Haute-Corse",
        '0496' => 'Marseille',
        '0497' => 'Alpes-Maritimes',
        '0498' => 'Var',
        '0499' => 'Hérault',
        '0508' => 'Saint-Pierre-et-Miquelon',
        '0516' => "Charente, Charente-Maritime, Deux-Sèvres, Vienne",
        '0517' => "Charente, Charente-Maritime, Deux-Sèvres, Vienne",
        '0518' => "Corrèze, Creuse, Haute-Vienne",
        '0519' => "Corrèze, Creuse, Haute-Vienne",
        '0524' => "Dordogne, Gironde, Landes, Lot-et-Garonne, Pyrénées-Atlantiques",
        '0531' => "Ariège, Aveyron, Haute-Garonne, Gers, Lot, Hautes-Pyrénées, Tarn, Tarn-et-Garonne",
        '0532' => "Ariège, Aveyron, Haute-Garonne, Gers, Lot, Hautes-Pyrénées, Tarn, Tarn-et-Garonne",
        '0533' => "Dordogne, Gironde, Landes, Lot-et-Garonne, Pyrénées-Atlantiques",
        '0534' => 'Haute-Garonne',
        '0535' => "Dordogne, Gironde, Landes, Lot-et-Garonne, Pyrénées-Atlantiques",
        '0536' => "Ariège, Aveyron, Haute-Garonne, Gers, Lot, Hautes-Pyrénées, Tarn, Tarn-et-Garonne",
        '0540' => "Dordogne, Gironde, Landes, Lot-et-Garonne, Pyrénées-Atlantiques",
        '0545' => 'Charente',
        '0546' => 'Charente-Maritime',
        '0547' => "Dordogne, Gironde, Landes, Lot-et-Garonne, Pyrénées-Atlantiques",
        '0549' => "Deux-Sèvres, Vienne",
        '0553' => "Dordogne, Lot-et-Garonne",
        '0554' => "Dordogne, Gironde, Landes, Lot-et-Garonne, Pyrénées-Atlantiques",
        '0555' => "Corrèze, Creuse, Haute-Vienne",
        '0556' => 'Gironde',
        '0557' => 'North of Gironde',
        '0558' => 'Landes',
        '0559' => 'Pyrénées-Atlantiques',
        '0561' => "Ariège, Haute-Garonne",
        '0562' => "Gers, Hautes-Pyrénées",
        '0563' => "Tarn, Tarn-et-Garonne",
        '0564' => "Dordogne, Gironde, Landes, Lot-et-Garonne, Pyrénées-Atlantiques",
        '0565' => "Aveyron, Lot",
        '0567' => "Ariège, Aveyron, Haute-Garonne, Gers, Lot, Hautes-Pyrénées, Tarn, Tarn-et-Garonne",
        '0568' => 'Telemarketing',
        '0569' => 'Telemarketing',
        '0579' => "Charente, Charente-Maritime, Deux-Sèvres, Vienne",
        '0581' => "Ariège, Aveyron, Haute-Garonne, Gers, Lot, Hautes-Pyrénées, Tarn, Tarn-et-Garonne",
        '0582' => "Ariège, Aveyron, Haute-Garonne, Gers, Lot, Hautes-Pyrénées, Tarn, Tarn-et-Garonne",
        '0586' => "Charente, Charente-Maritime, Deux-Sèvres, Vienne",
        '0587' => "Corrèze, Creuse, Haute-Vienne",
        '0590' => "Guadeloupe, Saint Barthélemy, Collectivity Saint Martin[8]",
        '0594' => 'French Guiana',
        '0596' => 'Martinique',
    ];

    /**
     * @override
     */
    public function getIsoAlpha2CountryCode(): string
    {
        return 'FR';
    }

    /**
     * @override
     */
    public function getCountryCallingCode(): string
    {
        return '33';
    }

    /**
     * @override
     */
    public function getTrunkCode(): string
    {
        return '0';
    }

    /**
     * @override
     */
    public function getNationalNumberLength(): int
    {
        return 10;
    }

    /**
     * See https://en.wikipedia.org/wiki/National_conventions_for_writing_telephone_numbers#France
     *
     * @override
     */
    public function formatIntl(TelephoneNumber $telephoneNumber): bool
    {
        // (By this point, the national number should contain only digits and be the correct length)
        $nationalNumber = $telephoneNumber->getNationalNumber();

        $telephoneNumberType = match (true) {
            Utils::startsWith(array_keys(self::AREA_CODES), $nationalNumber)
                => TelephoneNumber::TYPE_FIXED_LINE,

            Utils::startsWith('0[67]', $nationalNumber)
                => TelephoneNumber::TYPE_MOBILE,

            Utils::startsWith('0[89]', $nationalNumber)
                => TelephoneNumber::TYPE_NON_GEOGRAPHIC,

            default
                => null,
        };

        if (!$telephoneNumberType) {
            $telephoneNumber->noMatchForCountry();

            return false;
        }

        $countryCallingCode = $this->getCountryCallingCode();

        $numberClusters = str_split($nationalNumber, 2);

        $numberClusters[0] = (new StringObject($numberClusters[0]))
            ->deleteLeft($this->getTrunkCode())
            ->getValue()
        ;

        $telephoneNumber->matchForCountry(
            $this->getIsoAlpha2CountryCode(),
            $countryCallingCode,
            $telephoneNumberType,
            "+{$countryCallingCode} " . implode(' ', $numberClusters),
        );

        return true;
    }
}
