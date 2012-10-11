<?php

namespace EPS\JqGridBundle\FilterMapper;


class DateRangeFilterMapper extends AbstractFilterMapper
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
    $queryBuilder = $this->grid->getQueryBuilder();

    /**
     * @var \DateTime
     */
    $date = \DateTime::createFromFormat($this->grid->getDatePickerPhpFormat(), $rule['data']);
    switch ($rule['op']) {
      case 'eq':
      case 'cn':
        $tmpid = self::$cnt++;
        $queryBuilder
            ->andWhere($queryBuilder->expr()->eq($this->column->getFieldIndex(), ":{$this->column->getFieldName()}_{$tmpid}"))
            ->setParameter("{$this->column->getFieldName()}_{$tmpid}", $date->format('Y-m-d'));
        break;
      case 'gt':
        $tmpid = self::$cnt++;
        $queryBuilder
            ->andWhere($queryBuilder->expr()->gt($this->column->getFieldIndex(), ":{$this->column->getFieldName()}_{$tmpid}"))
            ->setParameter("{$this->column->getFieldName()}_{$tmpid}", $date->format('Y-m-d'));
        break;
      case 'lt':
        $tmpid = self::$cnt++;
        $queryBuilder
            ->andWhere($queryBuilder->expr()->lt($this->column->getFieldIndex(), ":{$this->column->getFieldName()}_{$tmpid}"))
            ->setParameter("{$this->column->getFieldName()}_{$tmpid}", $date->format('Y-m-d'));
        break;
      case 'ge':
        $tmpid = self::$cnt++;
        $queryBuilder
            ->andWhere($queryBuilder->expr()->gte($this->column->getFieldIndex(), ":{$this->column->getFieldName()}_{$tmpid}"))
            ->setParameter("{$this->column->getFieldName()}_{$tmpid}", $date->format('Y-m-d'));
        break;
      case 'le':
        $tmpid = self::$cnt++;
        $queryBuilder
            ->andWhere($queryBuilder->expr()->lte($this->column->getFieldIndex(), ":{$this->column->getFieldName()}_{$tmpid}"))
            ->setParameter("{$this->column->getFieldName()}_{$tmpid}", $date->format('Y-m-d'));
        break;
    }

//    $tmpid = self::$cnt++;
//
//    $queryBuilder
//        ->andWhere($queryBuilder->expr()->lte($this->column->getFieldIndex(), ":{$this->column->getFieldName()}_{$tmpid}"))
//        ->setParameter("{$this->column->getFieldName()}_{$tmpid}", $date->format('Y-m-d 23:59:59'));
    }
  }


  