<?php

namespace App\Exports;

use App\Model\Exhibitor;
use App\Model\FindAbout;
use App\Model\ReasonForAttending;
use App\Model\Register;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Throwable;

// for japan recommend
class WebinarHistoryLogExportTest implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    use Exportable;

    private $targerIds;
    private $emails;

    public function __construct($targerIds)
    {
        $this->targerIds = $targerIds;
        $this->emails = [
            "sureratk@impact.co.th",
            "pinyaphat.m.o@gmail.com",
            "peerayaphanp@impact.co.th",
            "namphueng.n@cmo-group.com",
            "thanakit@cu.co.th",
            "Wingyanw@impact.co.th",
            "prabuddha@unikwan.com",
            "shadowland_007@hotmail.com",
            "renoo@a2network.jp",
            "nopporn@cthaigroup.com",
            "anuchit_ming@hotmail.com",
            "kclye@gsc.com.my",
            "orathailiwen@gmail.com",
            "thongchai@sra.co.th",
            "thiphatai.c@cdg.co.th",
            "arpawee_s@cmc-ap.com",
            "thanusak.d@gmail.com",
            "marut@a2network.jp",
            "poraphan.s@ap.iij.com",
            "kritsada@csigroups.com",
            "ruangchai@agss.co.th",
            "dhavalchag@yahoo.com",
            "unchalee@tripetch-it.co.th",
            "tanaka@jscom.co.jp",
            "suthiluk.la@gmail.com",
            "teerapich@unifirms.co.th",
            "gary.leong@vitrox.com",
            "info35@dlab.co.th",
            "pirun_sb@hotmail.com",
            "auttapol@security.co.th",
            "prawit@tkm.co.th",
            "wuttichai.a@essco-solution.com",
            "boriboonmail@gmail.com",
            "Ask@keepital.com",
            "nutnaree.sr98@gmail.com",
            "mirza.sharif@mars.com.bd",
            "ekasit.v.vh@hitachi-hightech.com",
            "bodin@agss.co.th",
            "kasimas@betagro.com",
            "calvin_thng@hotmail.com",
            "maxwell.stewart@telos.com",
            "chawalit@tpp-th.com",
            "jasonmy@live.com",
            "jitkrit.k@icn.co.th",
            "livio.beffa@andritz.com",
            "preecha@unitedfoods.com",
            "ton_max_it@hotmail.com",
            "paman_jung@hotmail.com",
            "pichai@apollothai.com",
            "nutcha.t@pfc.premier.co.th",
            "withit.wiw@tcp.com",
            "cching@pldtglobal.com",
            "mongkol@genius-provider.com",
            "chalachaya.ma@nmc.co.th",
            "rachot@nstda.or.th",
            "fukuyasu@bigbeat.co.jp",
            "setthawit.p@greyhound.co.th",
            "sinhlu@aai.com.tw",
            "jasmy@fke.utm.my",
            "cdangprasert@gmail.com",
            "chawan@smartsolutioncomputer.com",
            "poranut4567@gmail.com",
            "chalerm88@hotmail.com",
            "chcji40@gmail.com",
            "prustayu.theamtong@dga.or.th",
            "phonsak@fujielectric.com",
            "k.matsuda@ubsecure.jp",
            "sadayuth.ax2.kam@ns-sus.com",
            "info@hoop-kitchen.com",
            "warawut3007@gmail.com",
            "anuwat@tripetch-it.co.th",
            "sathaporn.t@jasmine.com",
            "admin@inspirestudio.co.th",
            "99billion@gmail.com",
            "chieng@bangkokdeccon.com",
            "kridsda.sis@gmail.com",
            "sarawuth.p@b-connex.net",
            "arrerat@iconext.co.th",
            "jpcmoni@yahoo.com",
            "amornrat@csithai.com",
            "kc@achinasemicon.com",
            "somatat@gmail.com",
            "jovan.ang@sekisui.com",
            "narachut.t@thairath.tv",
            "chatwit.k@tangguijub.com",
            "itthiphol.r@gmail.com",
            "tairath@tot.co.th",
            "jiranthanin@strek.co.th",
            "sutee@proline.co.th",
            "jarastr@gmail.com",
            "winai.w@pacificinternet.com",
            "nuchjarin.in@kmitl.ac.th",
            "ayaji2.furukawa@toshiba.co.jp",
            "mrtanad@gmail.com",
            "outside_affairs@meg-it.jp",
            "shinichi.kawamoto.bh@hitachi.com",
            "sireeras@ttni.co.th",
            "somyod.iki@gmail.com",
            "calm-hom@hotmail.com",
            "lalita.a@acaya.ai",
            "k-arimura@optex.co.jp",
            "sukit.s@egat.co.th",
            "danupol.dus@gmail.com",
            "arthitng@hotmail.com",
            "worameth@bpsiiw.com",
            "worawut@tot.co.th",
            "mayho.hoh@lipisadvisors.com",
            "Thitipan.punyashthiti@sg.panasonic.com",
            "pongsatonakara@gmail.com",
            "mana.kharupong@brands-suntory.com",
            "rungkit.v@siamwinery.com",
            "panurat@eventthai.com",
            "yingyos@eposservice.com",
            "chavalit@tsoft.co.th",
            "charoensit.preecha@gmail.com",
            "m_inoue@bigbeat.co.jp",
            "samira.iffat@dsinnovators.com",
            "nikki.kelso@adia.org.au",
            "sirapopk@outlook.com",
            "Kamonporn@shownolimit.com",
            "padcharee_ut@amarintv.com",
            "siwapornlampang@gmail.com",
            "chote@nipponpaint.co.th",
            "anuwat70020@gmail.com",
            "jinnipa.int@gmail.com",
            "sitthikorn.h@hotmail.com",
            "malviyaharsh24@gmail.com",
            "manit@qixbox.com",
            "eastsiderz_xiii@hotmail.com",
            "thaiphateco@gmail.com",
            "narada.kamon@gmail.com",
            "songsaksub@hotmail.com",
            "jatuporn@supernap.co.th",
            "montri.zeroxe@gmail.com",
            "worapong_005@hotmail.com",
            "prapatsornt@impact.co.th",
            "theoeng@yahoo.com",
            "kanokkorn.in@hotmail.com",
            "ch.ado.1@hotmail.com",
            "chatapong.s@humanica.com",
            "soraphong19@gmail.com",
            "watcharapon.sutjaritjan.ap@nielsen.com",
            "kan@bigbeat-bkk.co.th",
            "ernestsiu@zotac.com",
            "m1393429595@lost-corona.com",
            "nutkamonpop@a2network.jp",
            "chutima@idin9.com",
            "nakkhain.ma@s-s-c.co.th",
            "nuti.khem@reedtradex.co.th",
            "panchanit.l@tkc-services.com",
            "crystal.tan.tt@hitachi.com",
            "udornk@scg.com",
            "sasipimonp@impact.co.th",
            "niphon60@yahoo.com",
            "mingnapha.ming@gmail.com",
            "manuel@vnuasiapacific.com",
            "worldwidewat@gmail.com",
            "draksanider@gmail.com",
            "tchairat@gmail.com",
            "oraya_t@cmc-ap.com",
            "somchaiy@maschoices.com",
            "jatin@expertscomputer.com",
            "vichaan1412@gmail.com",
            "wanna@isidsea.com",
            "reiko.matoike.cr@hitachi.com",
            "montri_kun@hotmail.com",
            "wichakc@gmail.com",
            "chonupsorn@b-en-g.co.th",
            "nano_gubpa@hotmail.com",
            "suphakit.p@20scoops.net",
            "winston@bizlink.com.sg",
            "cathyliao@phistek.com",
            "erp.surachet@thantawan.com",
            "Noriyasu_Kamioki@jma.or.jp",
            "pipat.kavin@gmail.com",
            "g.chinen@a2network.jp",
            "prakash@bulwarktech.com",
            "rathapol.k@chanwanich.com",
            "Poolzarp.chan@bumail.net",
            "pornphat.s@zubbsteel.com",
            "my_freedomus@hotmail.com",
            "sripai_d@dptf.co.th",
            "michael@netkasystem.com",
            "wisit_s@okayabkk.com",
            "walking2dreamup@gmail.com",
            "hajime.hirose.bq@hitachi.com",
            "thiambk@fiberopto.com",
            "ohmsiri1997@gmail.com",
            "sukanya@terabytenet.com",
            "yyanagisawa@gulfnet.co.th",
            "roger.bay@ericsson.com",
            "den@ovalthailand.com",
            "yuya.tahara@nagase.co.jp",
            "chiho.mihara@toshiba.co.jp",
            "thitirat.supiphatsakuk1@th.com",
            "m_tsurumi@bigbeat.co.jp",
            "andy@andyaditya.com",
            "siraawitt@gmail.com",
            "chanintorn.ji@ku.th",
            "sivaporn@tnt.co.th",
            "srunb@yahoo.com",
            "chakrin-c@hotmail.com",
            "abdellah2bouhnib@gmail.com",
            "ekkamol_p@hotmail.com",
            "phataramolwan@controla.co.th",
            "pisan@bangchak.co.th",
            "Karn@bigbeat-bkk.co.th",
            "wanchaiports@gmail.com",
            "kritchatach@regent-technology.com",
            "chan.poojenwong@gmail.com",
            "sirichai@panyapiwat.ac.th",
            "lookpou11@gmail.com",
            "nga.wilawan@gmail.com",
            "nun.nannapass.nj@gmail.com",
            "Ovident@me.com",
            "chokk_1@hotmail.com",
            "kittisit@hotmail.com",
            "saowaneesi@homepro.co.th",
            "impactdemo@varpevent.com",
            "support@ruk-com.in.th",
            "sukunya@iassistcorp.co.th",
            "marketing@wit.co.th",
            "ydyds@logtech.co.kr",
            "ybkim@irtkorea.com",
            "leeminhwan@platfos.com",
            "hiroshi.umezu@toshiba.co.jp",
            "parichat.y@n2n.co.th",
            "thaisales@iconext.co.th",
            "irene.ho@softspace.com.my",
            "isid_jrit@bigbeat.co.jp",
            "lexer_jrit@bigbeat.co.jp",
            "beng_jrit@bigbeat.co.jp",
            "jrit@bigbeat.co.jp",
            "hulft_jrit@bigbeat.co.jp",
            "chakritutairat@sourcemash.com",
            "albert@cloudbric.com",
            "vic.sithasanan@weareeverise.com",
            "bangladeshpavilion1@gmail.com",
            "bangladeshpavilion2@gmail.com",
            "bangladeshpavilion3@gmail.com",
            "hitachi_t_jrit@bigbeat.co.jp",
            "hitachi_s_jrit@bigbeat.co.jp",
            "cs@varpevent.com",
            "nubow.yoosatit@oracle.com"
        ];
    }

    public function collection()
    {
        $targerIds = $this->targerIds;
        $emails = $this->emails;

        $emails = array_unique($emails);

        // collect user target id
        // $visitorTargetId = [];
        // $exhibitorTargetId = [];
        // foreach ($loggerData as $detail) {
        //     try {
        //         if (strtoupper($detail["action"]) == "WEBINAR_VISIT") {
        //             if (strtoupper($detail["type"]) == "EXHIBITOR") {
        //                 array_push($exhibitorTargetId, $detail["refId"]);
        //             } else if (strtoupper($detail["type"]) == "USER") {
        //                 array_push($visitorTargetId, $detail["refId"]);
        //             }
        //         }
        //     } catch (Throwable $e) {
        //     }
        // }

        // remove duplicate id from arrays
        // $visitorTargetId = array_unique($visitorTargetId);
        // $exhibitorTargetId = array_unique($exhibitorTargetId);

        $exhibitorTargetId = [];
        $visitorTargetId = [];
        if ($targerIds) {
            $exhibitorTargetId = $targerIds["exhibitorId"];
            $visitorTargetId = $targerIds["visitorId"];
        }

        if ($emails) {
            $exhibitorTargetId = Exhibitor::whereIn("email", $emails)
            ->distinct()
            ->pluck("id");

            $visitorTargetId = Register::whereIn("email", $emails)
            ->distinct()
            ->pluck("id");
        }

        var_dump($exhibitorTargetId);
        var_dump($visitorTargetId);
        exit;

        $query = Exhibitor::select(
            "*",
            DB::raw('"Exhibitor" as type_str')
        )
            ->whereIn("id", $exhibitorTargetId)
            ->with([
                "country",
                "mainCateRef"
            ]);
        $exhibitor = $query->get()->toArray();

        $query = Register::select(
            "*",
            DB::raw('"Visitor" as type_str')
        )
            ->whereIn("id", $visitorTargetId)
            ->with([
                "country",
                "mainCateRef",
                "prefixName",
                "natureOfBusinessRef",
                "jobLevelRef",
                "jobFunctionRef",
                "role",
                "numberOfEmployeesRef",
                "budget"
            ]);
        $visitor = $query->get()->toArray();

        // create data mapping
        $reasonForAttMap = [];
        foreach (ReasonForAttending::all() as $tmp) {
            $reasonForAttMap["$tmp->id"] = $tmp->name_en;
        }

        $findOutAbountMap = [];
        foreach (FindAbout::all() as $tmp) {
            $findOutAbountMap["$tmp->id"] = $tmp->name_en;
        }

        // format data
        $data = [];
        foreach ($exhibitor as $detail) {
            array_push($data, [
                "id" => $detail["id"],
                "salutation" => "",
                "name" => $detail["name"],
                "position" => $detail["position"],
                "company" => $detail["company"],
                "address" => $detail["address"],
                "city" => "",
                "province" => "",
                "postalCode" => "",
                "countryName" => $detail["country"]["name"],
                "telephone" => $detail["m_mobile"],
                "mobile" => $detail["mobile"],
                "email" => $detail["email"],
                "website" => $detail["website"],
                "facebook" => $detail["facebook"],
                "youtube" => $detail["youtube"],
                "twitter" => $detail["twitter"],
                "linkedIn" => $detail["linkedin"],
                "companyInsdustry" => "",
                "jobLevel" => "",
                "jobFunction" => "",
                "roleProcess" => "",
                "numberEmp" => "",
                "cateInterest" => $detail["main_cate_ref"],
                "budget" => "",
                "reasonForAtt" => "",
                "findOutAbount" => "",
                "allowBusinessMatching" => "",
                "interestedToJoin" => "",
                "allowAccept" => "",
                "type" => $detail["type_str"]
            ]);
        }

        foreach ($visitor as $detail) {
            $prefixName = $detail["prefix_name"]["id"];
            if ($prefixName == 5) {
                $prefixName = $detail["prefix_name_other"];
            } else {
                $prefixName = $detail["prefix_name"]["name_en"];
            }

            $companyIndustry = $detail["nature_of_business_ref"]["id"];
            if ($companyIndustry == 24) {
                $companyIndustry = $detail["nature_of_business_other"];
            } else {
                $companyIndustry = $detail["nature_of_business_ref"]["name_en"];
            }

            $jobLevel = $detail["job_level_ref"]["id"];
            if ($jobLevel == 8) {
                $jobLevel = $detail["job_level_other"];
            } else {
                $jobLevel = $detail["job_level_ref"]["name_en"];
            }

            $jobFunction = $detail["job_function_ref"]["id"];
            if ($jobFunction == 17) {
                $jobFunction = $detail["job_function_other"];
            } else {
                $jobFunction = $detail["job_function_ref"]["name_en"];
            }

            $reasonForAtt = $this->makeTextFromStringArrayMap($detail["reason_for_attending"], $reasonForAttMap, $detail["reason_for_attending_other"], 9);
            $findOutAbout = $this->makeTextFromStringArrayMap($detail["find_out_about"], $findOutAbountMap, $detail["find_out_about_other"], 13);

            array_push($data, [
                "id" => $detail["id"],
                "salutation" => $prefixName,
                "name" => $detail["fname"] . " " . $detail["lname"],
                "position" => $detail["position"],
                "company" => $detail["company"],
                "address" => $detail["address"],
                "city" => $detail["city"],
                "province" => $detail["province"],
                "postalCode" => $detail["postal_code"],
                "countryName" => $detail["country"]["name"],
                "telephone" => $detail["telephone"],
                "mobile" => $detail["mobile"],
                "email" => $detail["email"],
                "website" => $detail["website"],
                "facebook" => "",
                "youtube" => "",
                "twitter" => "",
                "linkedIn" => "",
                "companyInsdustry" => $companyIndustry,
                "jobLevel" => $jobLevel,
                "jobFunction" => $jobFunction,
                "roleProcess" => $detail["role"]["name_en"],
                "numberEmp" => $detail["number_of_employees_ref"]["name"],
                "cateInterest" => $detail["main_cate_ref"],
                "budget" => $detail["budget"]["name_en"],
                "reasonForAtt" => $reasonForAtt,
                "findOutAbount" => $findOutAbout,
                "allowBusinessMatching" => $detail["allow_matching"],
                "interestedToJoin" => $detail["interested_to_join"],
                "allowAccept" => $detail["allow_accept"],
                "type" => $detail["type_str"]
            ]);
        }

        // add index
        $dataIndex = 1;
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]["index"] = $dataIndex;
            $dataIndex += 1;
        }

        return collect($data);
    }

    public function map($data): array
    {
        return [
            $data["index"],
            $data["salutation"],
            $data["name"],
            $data["position"],
            $data["company"],
            $data["address"],
            $data["city"],
            $data["province"],
            $data["postalCode"],
            $data["countryName"],
            $data["telephone"],
            $data["mobile"],
            $data["email"],
            $data["website"],
            $data["facebook"],
            $data["youtube"],
            $data["twitter"],
            $data["linkedIn"],
            $data["companyInsdustry"],
            $data["jobLevel"],
            $data["jobFunction"],
            $data["roleProcess"],
            $data["numberEmp"],
            $this->concatItemWord($data["cateInterest"], "name_en"),
            $data["budget"],
            $data["reasonForAtt"],
            $data["findOutAbount"],
            $data["allowBusinessMatching"],
            $data["interestedToJoin"],
            $data["allowAccept"],
            $data["type"]
        ];
    }

    public function headings(): array
    {
        return [
            [
                "index",
                "salutation",
                "name",
                "position",
                "company",
                "address",
                "city",
                "province",
                "postalCode",
                "countryName",
                "telephone",
                "mobile",
                "email",
                "website",
                "facebook",
                "youtube",
                "twitter",
                "linkedIn",
                "companyInsdustry",
                "jobLevel",
                "jobFunction",
                "roleProcess",
                "numberEmp",
                "cateInterest",
                "budget",
                "reasonForAtt",
                "findOutAbount",
                "allowBusinessMatching",
                "interestedToJoin",
                "allowAccept",
                "type"
            ]
        ];
    }

    public function concatItemWord($obj, $key)
    {
        $text = "";

        foreach ($obj as $detail) {
            $text .= $detail[$key] . " ,";
        }

        if ($text) {
            $text = substr($text, 0, -2);
        }

        return $text;
    }

    public function makeTextFromStringArrayMap($detail, $textMap, $strOther, $idOther)
    {
        $txt = "";
        if ($detail) {
            $detailToArray = substr($detail, 1, -1);
            $detailToArray = explode(",", $detailToArray);

            foreach ($detailToArray as $id) {
                if ($id == $idOther) {
                    $txt .= $strOther . " ,";
                } else {
                    try {
                        $txt .= $textMap[$id] . " ,";
                    } catch (Throwable $e) {
                    }
                }
            }

            $txt = substr($txt, 0, -2);
        }
        return $txt;
    }
}
