<?php declare(strict_types=1);

namespace Libtelex\Telex2\RuleSet;

use Libtelex\Telex2\RuleSetInterface;
use Libtelex\Telex2\TelephoneNumber;
use Libtelex\Telex2\Utils;

use function array_keys;
use function implode;
use function preg_replace;
use function str_starts_with;
use function strlen;
use function substr;

use const false;
use const null;
use const true;

/**
 * See:
 * - https://en.wikipedia.org/wiki/Telephone_numbers_in_the_United_Kingdom
 * - https://www.gov.uk/call-charges
 */
final class UnitedKingdom implements RuleSetInterface
{
    /**
     * Complete area codes.  3-5 digits, includes the trunk code.
     *
     * Initial source: https://countrycode.org/uk
     *
     * @var array<string,string>
     */
    private const AREA_CODES = [
        '01224' => 'Aberdeen',
        '01235' => 'Abingdon',
        '01339' => 'Aboyne',
        '01252' => 'Aldershot',
        '01507' => 'Alford (Lincolnshire)',
        '01259' => 'Alloa',
        '01420' => 'Alton',
        '01269' => 'Ammanford',
        '01264' => 'Andover',
        '01461' => 'Annan',
        '01241' => 'Arbroath',
        '01294' => 'Ardrossan',
        '01301' => 'Arrochar',
        '01276' => 'Ascot',
        '01335' => 'Ashbourne',
        '01364' => 'Ashburton',
        '01233' => 'Ashford',
        '01297' => 'Axminster',
        '01296' => 'Aylesbury',
        '01292' => 'Ayr',
        '01295' => 'Banbury',
        '01330' => 'Banchory',
        '01261' => 'Banff',
        '01248' => 'Bangor (N. Wales)',
        '01341' => 'Barmouth',
        '01226' => 'Barnsley',
        '01271' => 'Barnstable',
        '01229' => 'Barrow-in-Furness',
        '01446' => 'Barry',
        '01256' => 'Basingstoke',
        '01225' => 'Bath',
        '01506' => 'Bathgate',
        '01234' => 'Bedford',
        '01434' => 'Bellingham',
        '01289' => 'Berwick-on-Tweed',
        '01299' => 'Bewdley',
        '01237' => 'Bideford',
        '01388' => 'Bishop Auckland',
        '01279' => 'Bishops Stortford',
        '01254' => 'Blackburn',
        '01253' => 'Blackpool',
        '01250' => 'Blairgowrie',
        '01258' => 'Blandford',
        '01208' => 'Bodmin',
        '01204' => 'Bolton',
        '01423' => 'Boroughbridge',
        '01205' => 'Boston',
        '01202' => 'Bournemouth',
        '01451' => 'Bourton-on-the-Water',
        '01344' => 'Bracknell',
        '01274' => 'Bradford',
        '01376' => 'Braintree',
        '01356' => 'Brechin',
        '01277' => 'Brentwood',
        '01278' => 'Bridgwater',
        '01262' => 'Bridlington',
        '01308' => 'Bridport',
        '01273' => 'Brighton',
        '01275' => 'Bristol',
        '01471' => 'Broadford',
        '01508' => 'Brooke',
        '01280' => 'Buckingham',
        '01288' => 'Bude',
        '01395' => 'BudleighSalterton',
        '01425' => 'Burley',
        '01282' => 'Burnley',
        '01543' => 'Burntwood',
        '01283' => 'Burton-on-Trent',
        '01284' => 'Bury-St-Edmunds',
        '01298' => 'Buxton',
        '01286' => 'Caernarvon',
        '01223' => 'Cambridge',
        '01227' => 'Canterbury',
        '01239' => 'Cardigan',
        '01228' => 'Carlisle',
        '01267' => 'Carmarthen',
        '01556' => 'Castle Douglas',
        '01300' => 'Cerne Abbas',
        '01460' => 'Chard',
        '01245' => 'Chelmsford',
        '01242' => 'Cheltenham',
        '01244' => 'Chester',
        '01246' => 'Chesterfield',
        '01243' => 'Chichester',
        '01608' => 'Chipping Norton',
        '01249' => 'Chippenham',
        '01285' => 'Cirencester',
        '01255' => 'Clacton-on-Sea',
        '01200' => 'Clitheroe',
        '01437' => 'Clynderwen',
        '01530' => 'Coalville',
        '01236' => 'Coatbridge',
        '01206' => 'Colchester',
        '01492' => 'Colwyn Bay',
        '01260' => 'Congleton',
        '01477' => 'Congleton',
        '01207' => 'Consett',
        '01257' => 'Coppull',
        '01490' => 'Corwen',
        '01340' => 'Craigellachie',
        '01363' => 'Crediton',
        '01270' => 'Crewe',
        '01263' => 'Cromer',
        '01290' => 'Cumnock',
        '01325' => 'Darlington',
        '01332' => 'Derby',
        '01362' => 'Dereham',
        '01380' => 'Devizes',
        '01349' => 'Dingwall',
        '01379' => 'Diss',
        '01485' => 'Docking',
        '01354' => 'Doddington',
        '01302' => 'Doncaster',
        '01305' => 'Dorchester',
        '01304' => 'Dover',
        '01366' => 'Downham Market',
        '01377' => 'Driffield',
        '01398' => 'Dulverton',
        '01389' => 'Dumbarton',
        '01387' => 'Dumfries',
        '01368' => 'Dunbar',
        '01382' => 'Dundee',
        '01383' => 'Dunfermline',
        '01350' => 'Dunkeld',
        '01369' => 'Dunoon',
        '01361' => 'Duns',
        '01453' => 'Dursley',
        '01347' => 'Easingwold',
        '01342' => 'East Grinstead',
        '01355' => 'East Kilbride',
        '01357' => 'East Kilbride',
        '01323' => 'Eastbourne',
        '01470' => 'Edinbane',
        '01343' => 'Elgin',
        '01358' => 'Ellon',
        '01353' => 'Ely',
        '01372' => 'Esher',
        '01392' => 'Exeter',
        '01328' => 'Fakenham',
        '01324' => 'Falkirk',
        '01326' => 'Falmouth',
        '01329' => 'Fareham',
        '01489' => 'Fareham',
        '01367' => 'Faringdon',
        '01348' => 'Fishguard',
        '01303' => 'Folkestone',
        '01561' => 'Fordoun',
        '01307' => 'Forfar',
        '01309' => 'Forres',
        '01320' => 'Fort Augustus',
        '01397' => 'Fort William',
        '01381' => 'Fortrose',
        '01346' => 'Fraserburgh',
        '01373' => 'Frome',
        '01427' => 'Gainsborough',
        '01445' => 'Gairloch',
        '01465' => 'Girvan',
        '01458' => 'Glastonbury',
        '01456' => 'Glen Urquhart',
        '01457' => 'Glossop',
        '01452' => 'Gloucester',
        '01408' => 'Golspie',
        '01405' => 'Goole',
        '01476' => 'Grantham',
        '01479' => 'Grantown-on-Spey',
        '01474' => 'Gravesend',
        '01371' => 'Great Dunmow',
        '01488' => 'Great Shefford',
        '01493' => 'Great Yarmouth',
        '01475' => 'Greenock',
        '01472' => 'Grimsby',
        '01481' => 'Guernsey',
        '01483' => 'Guildford',
        '01287' => 'Guisborough',
        '01422' => 'Halifax',
        '01501' => 'Harthill',
        '01429' => 'Hartlepool',
        '01428' => 'Haslemere',
        '01424' => 'Hastings',
        '01433' => 'Hathersage',
        '01440' => 'Haverhill',
        '01450' => 'Hawick',
        '01497' => 'Hay-on-Wye',
        '01444' => 'Haywards Heath',
        '01435' => 'Heathfield',
        '01436' => 'Helensburgh',
        '01431' => 'Helmsdale',
        '01439' => 'Helmsley',
        '01432' => 'Hereford',
        '01494' => 'High Wycombe',
        '01455' => 'Hinckley',
        '01462' => 'Hitchin',
        '01406' => 'Holbeach',
        '01409' => 'Holsworthy',
        '01407' => 'Holyhead',
        '01400' => 'Honington',
        '01404' => 'Honiton',
        '01964' => 'Hornsea/Patrington',
        '01403' => 'Horsham',
        '01484' => 'Huddersfield',
        '01482' => 'Hull',
        '01480' => 'Huntingdon',
        '01466' => 'Huntly',
        '01464' => 'Insch',
        '01499' => 'Inveraray',
        '01463' => 'Inverness',
        '01467' => 'Inverurie',
        '01473' => 'Ipswich',
        '01624' => 'Isle of Man',
        '01983' => 'Isle of Wight',
        '01505' => 'Johnstone',
        '01535' => 'Keighley',
        '01542' => 'Keith',
        '01573' => 'Kelso',
        '01539' => 'Kendal',
        '01536' => 'Kettering',
        '01538' => 'Kettering',
        '01360' => 'Killearn',
        '01567' => 'Killin',
        '01469' => 'Killingholme',
        '01563' => 'Kilmarnock',
        '01553' => 'Kings Lynn',
        '01548' => 'Kingsbridge',
        '01544' => 'Kington',
        '01540' => 'Kingussie',
        '01577' => 'Kinross',
        '01557' => 'Kirkcudbright',
        '01575' => 'Kirriemuir',
        '01438' => 'Knebworth',
        '01547' => 'Knighton',
        '01565' => 'Knutsford',
        '01337' => 'Ladybank',
        '01528' => 'Laggan',
        '01549' => 'Lairg',
        '01570' => 'Lampeter',
        '01555' => 'Lanark',
        '01524' => 'Lancaster',
        '01564' => 'Lapworth',
        '01578' => 'Lauder',
        '01566' => 'Launceston',
        '01525' => 'Leighton Buzzard',
        '01568' => 'Leominster',
        '01522' => 'Lincoln',
        '01545' => 'Llanarth',
        '01558' => 'Llandeilo',
        '01550' => 'Llandovery',
        '01559' => 'Llandysul',
        '01554' => 'Llanelli',
        '01520' => 'Lochcarron',
        '01546' => 'Lochgilphead',
        '01571' => 'Lochinver',
        '01576' => 'Lockerbie',
        '01503' => 'Looe',
        '01509' => 'Loughborough',
        '01502' => 'Lowestoft',
        '01625' => 'Macclesfield',
        '01654' => 'Machynlleth',
        '01954' => 'Madingley',
        '01628' => 'Maidenhead',
        '01622' => 'Maidstone',
        '01430' => 'Market Weighton',
        '01442' => 'Markyate',
        '01672' => 'Marlborough',
        '01526' => 'Martin',
        '01664' => 'Melton Mowbray',
        '01642' => 'Middlesbrough',
        '01352' => 'Mold',
        '01600' => 'Monmouth',
        '01670' => 'Morpeth',
        '01560' => 'Moscow',
        '01491' => 'Nettlebed',
        '01663' => 'New Mills',
        '01635' => 'Newbury',
        '01293' => 'Newdigate',
        '01306' => 'Newdigate',
        '01620' => 'North Berwick',
        '01603' => 'Norwich',
        '01572' => 'Oakham',
        '01865' => 'Oxford',
        '01359' => 'Pakenham',
        '01333' => 'Peat Inn',
        '01334' => 'Peat Inn',
        '01885' => 'Pencombe',
        '01768' => 'Penrith',
        '01738' => 'Perth',
        '01730' => 'Petersfield',
        '01752' => 'Plymouth',
        '01759' => 'Pocklington',
        '01495' => 'Pontypool',
        '01443' => 'Pontypridd',
        '01496' => 'Port Ellen',
        '01478' => 'Portree',
        '01454' => 'Rangeworthy',
        '01527' => 'Redditch',
        '01209' => 'Redruth',
        '01706' => 'Rochdale',
        '01799' => 'Saffron Walden',
        '01722' => 'Salisbury',
        '01767' => 'Sandy',
        '01732' => 'Sevenoaks',
        '01291' => 'Shirenewton',
        '01394' => 'Shottisham',
        '01743' => 'Shrewsbury',
        '01695' => 'Skelmersdale',
        '01529' => 'Sleaford',
        '01702' => 'Southend-on-Sea',
        '01704' => 'Southport',
        '01727' => 'St Albans',
        '01726' => 'St Austell',
        '01268' => 'Stanford-le-Hope',
        '01375' => 'Stanford-le-Hope',
        '01786' => 'Stirling',
        '01569' => 'Stonehaven',
        '01384' => 'Stourbridge',
        '01386' => 'Stourbridge',
        '01562' => 'Stourbridge',
        '01449' => 'Stowmarket',
        '01789' => 'Stratford-upon-Avon',
        '01997' => 'Strathpeffer',
        '01322' => 'Swanley',
        '01793' => 'Swindon',
        '01823' => 'Taunton',
        '01822' => 'Tavistock',
        '01875' => 'Tranent',
        '01844' => 'Thame',
        '01872' => 'Truro',
        '01892' => 'Tunbridge Wells',
        '01895' => 'Uxbridge',
        '01487' => 'Warboys',
        '01920' => 'Ware',
        '01925' => 'Warrington',
        '01926' => 'Warwick',
        '01923' => 'Watford',
        '01327' => 'Weedon',
        '01903' => 'Worthing',
        '01932' => 'Weybridge',
        '01962' => 'Winchester',
        '01993' => 'Witney',
        '01935' => 'Yeovil',
        '0121' => 'Birmingham',
        '0117' => 'Bristol',
        '0191' => 'Durham',
        '0131' => 'Edinburgh',
        '0141' => 'Glasgow',
        '0113' => 'Leeds',
        '0116' => 'Leicester',
        '0151' => 'Liverpool',
        '0161' => 'Manchester',
        '0115' => 'Nottingham',
        '0118' => 'Reading',
        '0114' => 'Sheffield',
        '029' => 'Cardiff',
        '024' => 'Coventry',
        '020' => 'London',
        '028' => 'Northern Ireland',
        '023' => 'Portsmouth',
    ];

    /**
     * @override
     */
    public function getCountryCallingCode(): string
    {
        return '44';
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
     * @return int[]
     */
    public function getNationalNumberLength(): array
    {
        return [11, 10];
    }

    /**
     * See https://en.wikipedia.org/wiki/National_conventions_for_writing_telephone_numbers#United_Kingdom
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

            Utils::startsWith('07\d{3}', $nationalNumber, $prefix)
                => TelephoneNumber::TYPE_MOBILE,

            Utils::startsWith('03[0347]\d|080[08]|084[345]|087[01]', $nationalNumber, $prefix)
                => TelephoneNumber::TYPE_NON_GEOGRAPHIC,

            default
                => null,
        };

        if (!$telephoneNumberType) {
            $telephoneNumber->noMatchForCountry();

            return false;
        }

        $numberClusters = [];

        if (TelephoneNumber::TYPE_FIXED_LINE === $telephoneNumberType && str_starts_with($prefix, '02')) {
            // (Special fixed-line-only format)
            $numberClusters = Utils::chunk($nationalNumber, patternOrLength: [3, 4, 4]);
        } else {
            $lengthOfPrefix = strlen($prefix);
            $restOfNationalNumber = substr($nationalNumber, $lengthOfPrefix);

            if (7 === strlen($restOfNationalNumber)) {
                $numberClusters = [$prefix, ...Utils::chunk($restOfNationalNumber, patternOrLength: [3, 4])];
            } else {
                // Use the 'default', `<area-code><rest>` format
                $numberClusters = Utils::chunk($nationalNumber, patternOrLength: [$lengthOfPrefix, /* (rest) */]);
            }
        }

        $countryCallingCode = $this->getCountryCallingCode();
        // Remove the trunk code
        $numberClusters[0] = preg_replace('/^' . $this->getTrunkCode() . '/', '', $numberClusters[0]);

        $telephoneNumber->matchForCountry(
            $countryCallingCode,
            $telephoneNumberType,
            "+{$countryCallingCode} " . implode(' ', $numberClusters),
        );

        return true;
    }
}
