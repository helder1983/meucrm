<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;

use App\Helpers\Format;
use App\Helpers\StructureResult;

use App\Http\Requests\LeadsSearchRequest;
use App\Http\Requests\LeadsCreateOrEditRequest;

use App\Models\Product;
use App\Models\User;
use App\Models\Field;
use App\Models\Lead;
use App\Models\AuxList;
use Illuminate\Support\Env;

/**
 *
 */
class LeadsController extends Controller
{
    /**
     * @var string
     */
    protected string $module = 'leads';

    /**
     * @param LeadsSearchRequest $request
     * @return View
     */
    public function result(LeadsSearchRequest $request): View
    {
        $resultStructure = $this->listResult($this->module, true);
        $viewData = compact(
            'resultStructure'
        );

        return view($this->module.'.result', $viewData);
    }

    /**
     * @param $module
     * @param false $structure
     * @return array
     */
    public function listResult($module, bool $structure = false): array
    {
        $where = [];

        $filters = app('request')->All();
        unset($filters['pagination'], $filters['_order'], $filters['_direction'], $filters['page'], $filters['module'], $filters['hostname']);

        foreach ($filters as $key => $val) {
            if (isset($val)) {
                if (substr_count($key, 'periodo') > 0) {
                    $datePeriod = explode('-', $val);
                    if ($key === 'periodo_criacao') {
                        $key = 'date_entered';
                    }

                    $dateStart = trim($datePeriod[0]);
                    $dateEnd = trim($datePeriod[1]);

                    //date start
                    $where[$key][] = ['>=', Format::bankDate($dateStart)];

                    //date end
                    $where[$key][] = ['<=', Format::bankDate($dateEnd)];
                } elseif ($key === 'first_name') {
                    $where[$key][] = ['LIKE', '%'.$val.'%'];
                } else {
                    $where[$key][] = ['=', $val];
                }
            }
        }

        // Naturezas
        $leads  = new Lead();
        $leads = $leads->paginateWithSearch([
            /*'accounts' => [
				'foreign_id' => 'accounts.id',
				'group' => 'leads.account_id',
				'fields' => [
					'name'
					'reserva', 'draft_deadline',
					'vgm_deadline', 'carga_deadline'
				],
			],*/
					/*'reserva', 'draft_deadline',
					'vgm_deadline', 'carga_deadline'*/
				/*],
			],
			/*'aux_list' => [
				'foreign_id' => [
					'table' => 'leads',
					'column' => 'processo_exportacao_id'
				],
				'group' => 'expor_processos.id',
				'fields' => [
					'registro_numero',
				]
			]*/
        ], $where);

        $resultStructure = StructureResult::resultData($module, $leads, $filters, 'idnum');
        return $structure ? $resultStructure : $leads;
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $product = new Product();
        $user = new User();
        $auxList = new AuxList();

        $usersList = $user->getUserList();
        $productList = $product->getProductList();
        $statusLeadList = $auxList::getAuxList('status_lead_list');
        $ratingList = $auxList::getAuxList('rating_list');
        $leadSourceDom = $auxList::getAuxList('lead_source_dom');
        $statusImovelList = $auxList::getAuxList('status_imovel_list');
        $temImovelList = $auxList::getAuxList('tem_imovel_list');

        $module = $this->module;

        $viewData = compact(
            'statusLeadList',
            'ratingList',
            'leadSourceDom',
            'statusImovelList',
            'temImovelList',
            'productList',
            'usersList',
            'module'
        );

        return view($module.'.index', $viewData);
    }

    /**
     * @return View
     */
    public function create(): View
    {
        $module = $this->module;

        $product = new Product();

        $productList = $product->getProductList('-- Selecione --');

        $fields = (new Field())->modulesFields([
            $module,
            'LeadsFinanciamento',
            'LeadsHomeequity',
            'LeadsConsorcio'
        ]);

        $viewData = compact('module', 'productList', 'fields');

        return view($module.'.create', $viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function store(LeadsCreateOrEditRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @return View
     */
    public function show(): View
    {
        $id = app('request')->get('id');

        $module = $this->module;

        $lead = (new Lead())->find($id);
        $account = $lead->account()->first();
        $status = $lead->status()->first();
        $emailAddrBeanRel = $lead->emailAddrBeanRel()->first();
        $emailAddress = $emailAddrBeanRel->emailAddress()->first();
        $sexo = $lead->sexo()->first();

        $viewData = compact('module', 'id', 'lead', 'account', 'status', 'emailAddress', 'sexo');

        return view($module.'.show', $viewData);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
