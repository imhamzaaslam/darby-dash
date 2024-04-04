<?php

namespace App\Enums;

enum InvoiceLineType
{
    case COMMISSION;
    case COMPENSATION;
    case COMPENSATION_LOST_GOODS;
    case CORRECTION_COMMISSION;
    case CORRECTION_DISTRIBUTION_BY_BOLCOM_LABEL;
    case CORRECTION_FIRST_MILE;
    case CORRECTION_OUTBOUND;
    case CORRECTION_PICK_PACK;
    case CORRECTION_PLAZA_RETURN_SHIPPING_LABEL;
    case CORRECTION_RETAILER_AUTHORIZATION;
    case CORRECTION_SHIPMENT_LABEL;
    case CORRECTION_SHIPMENT_LABEL_POST;
    case CORRECTION_TURNOVER;
    case DAY_OFFER_BOOSTER;
    case DAY_OFFER_BOOSTER_CORRECTION;
    case DAY_OFFER_BOOSTER_DISCOUNT;
    case DISPLAY_OFFSITE;
    case DISPLAY_OFFSITE_CORRECTION;
    case DISPLAY_OFFSITE_DISCOUNT;
    case DISPLAY_ONSITE;
    case DISPLAY_ONSITE_CORRECTION;
    case DISPLAY_ONSITE_DISCOUNT;
    case DISTRIBUTION_BY_BOLCOM_LABEL;
    case FIRST_MILE;
    case LOGISTICAL_CHARGE;
    case NCK_STOCK;
    case ONLINE_MAGAZINES;
    case ONLINE_MAGAZINES_CORRECTION;
    case ONLINE_MAGAZINES_DISCOUNT;
    case OUTBOUND;
    case PICK_PACK;
    case PLAZA_RETURN_SHIPPING_LABEL;
    case PROMO_BOOSTER_PLUS;
    case PROMO_BOOSTER_PLUS_CORRECTION;
    case PROMO_BOOSTER_PLUS_DISCOUNT;
    case PROMO_BOOSTER_PLUS_HALF;
    case PROMO_BOOSTER_PLUS_HALF_CORRECTION;
    case PROMO_BOOSTER_PLUS_HALF_DISCOUNT;
    case RETAILER_AUTHORIZATION;
    case SHIPMENT_LABEL;
    case SHIPMENT_LABEL_POST;
    case SOCIAL_ADVERTISING;
    case SOCIAL_ADVERTISING_CORRECTION;
    case SOCIAL_ADVERTISING_DISCOUNT;
    case SPONSORED_PRODUCTS;
    case SPONSORED_PRODUCTS_CORRECTION;
    case SPONSORED_PRODUCTS_DISCOUNT;
    case STOCK;
    case TURNOVER;

    public static function getByGeneralLedger(GeneralLedger $generalLedger): array
    {
        return match ($generalLedger) {
            GeneralLedger::OTHER_SALES_COSTS => [
                self::COMMISSION,
                self::CORRECTION_COMMISSION,
                self::RETAILER_AUTHORIZATION,
                self::CORRECTION_RETAILER_AUTHORIZATION
            ],
            GeneralLedger::OUTSOURCED_WORK => [
                self::STOCK,
                self::NCK_STOCK,
                self::PICK_PACK,
                self::CORRECTION_PICK_PACK,
                self::LOGISTICAL_CHARGE,
                self::FIRST_MILE,
                self::CORRECTION_FIRST_MILE,
            ],
            GeneralLedger::COMPENSATIONS => [
                self::COMPENSATION,
                self::COMPENSATION_LOST_GOODS,
            ],
            GeneralLedger::PACKAGING_COSTS => [
                self::OUTBOUND,
                self::CORRECTION_OUTBOUND,
                self::PLAZA_RETURN_SHIPPING_LABEL,
                self::CORRECTION_PLAZA_RETURN_SHIPPING_LABEL,
                self::SHIPMENT_LABEL,
                self::SHIPMENT_LABEL_POST,
                self::CORRECTION_SHIPMENT_LABEL,
                self::CORRECTION_SHIPMENT_LABEL_POST,
                self::DISTRIBUTION_BY_BOLCOM_LABEL,
                self::CORRECTION_DISTRIBUTION_BY_BOLCOM_LABEL,
            ],
            GeneralLedger::ADVERTISING_COSTS => [
                self::DAY_OFFER_BOOSTER,
                self::DAY_OFFER_BOOSTER_CORRECTION,
                self::DAY_OFFER_BOOSTER_DISCOUNT,
                self::DISPLAY_OFFSITE,
                self::DISPLAY_OFFSITE_CORRECTION,
                self::DISPLAY_OFFSITE_DISCOUNT,
                self::DISPLAY_ONSITE,
                self::DISPLAY_ONSITE_CORRECTION,
                self::DISPLAY_ONSITE_DISCOUNT,
                self::ONLINE_MAGAZINES,
                self::ONLINE_MAGAZINES_CORRECTION,
                self::ONLINE_MAGAZINES_DISCOUNT,
                self::PROMO_BOOSTER_PLUS,
                self::PROMO_BOOSTER_PLUS_CORRECTION,
                self::PROMO_BOOSTER_PLUS_DISCOUNT,
                self::PROMO_BOOSTER_PLUS_HALF,
                self::PROMO_BOOSTER_PLUS_HALF_CORRECTION,
                self::PROMO_BOOSTER_PLUS_HALF_DISCOUNT,
                self::SOCIAL_ADVERTISING,
                self::SOCIAL_ADVERTISING_CORRECTION,
                self::SOCIAL_ADVERTISING_DISCOUNT,
                self::SPONSORED_PRODUCTS,
                self::SPONSORED_PRODUCTS_CORRECTION,
                self::SPONSORED_PRODUCTS_DISCOUNT,
            ],
            default => [],
        };
    }
}
