<?php
namespace EPS\JqGridBundle\FilterMapper;
class ComparisionFilterMapper extends AbstractFilterMapper
{

    /**
     * @var integer
     */
    protected static $cnt = 0;
    
    /**
     * @param array $rule
     * @param string $groupOperator
     *
     * @return mixed
     */
    public function execute(array $rule, $groupOperator = 'OR')
    {
        $parameter = $rule['data'];
        $queryBuilder = $this->grid->getQueryBuilder();
        $expression = $this->grid->getQueryBuilder()->expr();

        $tmpid = self::$cnt++;
        
        switch ($rule['op']) {
            case 'eq':
                $where = $expression->eq($this->column->getFieldIndex(), ":{$this->column->getFieldName()}_{$tmpid}");
                break;

            case 'ne':
                $where = $expression->neq($this->column->getFieldIndex(), ":{$this->column->getFieldName()}_{$tmpid}");
                break;

            case 'lt':
                $where = $expression->lt($this->column->getFieldIndex(), ":{$this->column->getFieldName()}_{$tmpid}");
                break;

            case 'le':
                $where = $expression->lte($this->column->getFieldIndex(), ":{$this->column->getFieldName()}_{$tmpid}");
                break;

            case 'gt':
                $where = $expression->gt($this->column->getFieldIndex(), ":{$this->column->getFieldName()}_{$tmpid}");
                break;

            case 'ge':
                $where = $expression->gte($this->column->getFieldIndex(), ":{$this->column->getFieldName()}_{$tmpid}");
                break;

            case 'bw':
                $where = 'LOWER(' . $this->column->getFieldIndex() . ") LIKE :{$this->column->getFieldName()}_{$tmpid}";
                $parameter = strtolower($rule['data']) . '%';
                break;

            case 'bn':
                $where = 'LOWER(' . $this->column->getFieldIndex() . ") NOT LIKE :{$this->column->getFieldName()}_{$tmpid}";
                $parameter = strtolower($rule['data']) . '%';
                break;

            case 'nu':
                $where = $expression
                        ->orX(
                                $expression
                                        ->eq($this->column->getFieldIndex(), ":{$this->column->getFieldName()}_{$tmpid}"),
                                $this->column->getFieldIndex() . ' IS NULL');

                $parameter = '';
                break;

            case 'nn':
                $where = $expression
                        ->andX(
                                $expression
                                        ->neq($this->column->getFieldIndex(), ":{$this->column->getFieldName()}_{$tmpid}"),
                                $this->column->getFieldIndex() . ' IS NOT NULL');

                $parameter = '';
                break;

            case 'in':
                if (false !== strpos($rule['data'], ',')) {

                    $where = $expression->in($this->column->getFieldIndex(), ":{$this->column->getFieldName()}_{$tmpid}");
                    $parameter = explode(',', $rule['data']);

                } elseif (false !== strpos($rule['data'], '-')) {

                    $where = $expression->between($this->column->getFieldIndex(), ":start_{$tmpid}", ":end_{$tmpid}");

                    list($start, $end) = explode('-', $rule['data']);

                    $queryBuilder->setParameter('start_'.$tmpid, $start);
                    $queryBuilder->setParameter('end_'.$tmpid, $end);

                    unset($parameter);
                }
                break;

            case 'ni':
                if (false !== strpos($rule['data'], ',')) {

                    $where = $expression->notIn($this->column->getFieldIndex(), ":{$this->column->getFieldName()}_{$tmpid}");
                    $parameter = explode(',', $rule['data']);

                } elseif (false !== strpos($rule['data'], '-')) {

                    $where = $expression->orX($this->column->getFieldIndex() . "< :start_{$tmpid}", $this->column->getFieldIndex() . "> :end_{$tmpid}");
                    list($start, $end) = explode('-', $rule['data']);
                    $queryBuilder->setParameter('start_'.$tmpid, $start);
                    $queryBuilder->setParameter('end_'.$tmpid, $end);
                    unset($parameter);
                }

                break;

            case 'ew':
                $where = 'LOWER(' . $this->column->getFieldIndex() . ") LIKE :{$this->column->getFieldName()}_{$tmpid}";
                $parameter = '%' . strtolower($rule['data']);
                break;

            case 'en':
                $where = 'LOWER(' . $this->column->getFieldIndex() . ") NOT LIKE :{$this->column->getFieldName()}_{$tmpid}";
                $parameter = '%' . strtolower($rule['data']);
                break;

            case 'nc':
                $where = 'LOWER(' . $this->column->getFieldIndex() . ") NOT LIKE :{$this->column->getFieldName()}_{$tmpid}";
                $parameter = '%' . strtolower($rule['data']) . '%';
                break;

            default: // Case 'cn' (contains)
                $where = 'LOWER(' . $this->column->getFieldIndex() . ") LIKE :{$this->column->getFieldName()}_{$tmpid}";
                $parameter = '%' . strtolower($rule['data']) . '%';
        }

        if ('OR' == $groupOperator) {
            $queryBuilder->orWhere($where);
        } else {
            $queryBuilder->andWhere($where);
        }

        if (isset($parameter)) {
            $queryBuilder->setParameter($this->column->getFieldName().'_'.$tmpid, $parameter);
        }
    }
}
