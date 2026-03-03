<?php
	use yii\db\Migration;

	class m190805_052927_Relations extends Migration{

		public function init(){
			$this->db = 'db';
			parent::init();
		}

		public function safeUp(){
			$this->addForeignKey('fk_balance_created_by',
			                     '{{%balance}}', 'created_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_balance_part_id',
			                     '{{%balance}}', 'part_id',
			                     '{{%part}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_balance_warehouse_id',
			                     '{{%balance}}', 'warehouse_id',
			                     '{{%warehouse}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_container_created_by',
			                     '{{%container}}', 'created_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_container_updated_by',
			                     '{{%container}}', 'updated_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_container_invoice_container_id',
			                     '{{%container_invoice}}', 'container_id',
			                     '{{%container}}', 'id',
			                     'CASCADE', 'RESTRICT'
			);
			$this->addForeignKey('fk_container_invoice_delivery_term_id',
			                     '{{%container_invoice}}', 'delivery_term_id',
			                     '{{%delivery_term}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_container_invoice_arrived_by',
			                     '{{%container_invoice}}', 'arrived_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_container_invoice_document_id',
			                     '{{%container_invoice}}', 'document_id',
			                     '{{%document}}', 'id',
			                     'SET NULL', 'RESTRICT'
			);
			$this->addForeignKey('fk_container_invoice_invoice_id',
			                     '{{%container_invoice}}', 'invoice_id',
			                     '{{%invoice}}', 'id',
			                     'CASCADE', 'RESTRICT'
			);
			$this->addForeignKey('fk_container_invoice_received_by',
			                     '{{%container_invoice}}', 'received_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_container_invoice_ship_mode_id',
			                     '{{%container_invoice}}', 'ship_mode_id',
			                     '{{%ship_mode}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_container_invoice_shipped_by',
			                     '{{%container_invoice}}', 'shipped_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_contract_buyer_id',
			                     '{{%contract}}', 'buyer_id',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_contract_contract_source_id',
			                     '{{%contract}}', 'contract_source_id',
			                     '{{%contract_source}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_contract_contract_subject_id',
			                     '{{%contract}}', 'contract_subject_id',
			                     '{{%contract_subject}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_contract_created_by',
			                     '{{%contract}}', 'created_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_contract_currency_id',
			                     '{{%contract}}', 'currency_id',
			                     '{{%currency}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_contract_delivery_term_id',
			                     '{{%contract}}', 'delivery_term_id',
			                     '{{%delivery_term}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_contract_payment_term_id',
			                     '{{%contract}}', 'payment_term_id',
			                     '{{%payment_term}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_contract_supplier_id',
			                     '{{%contract}}', 'supplier_id',
			                     '{{%supplier}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_contract_updated_by',
			                     '{{%contract}}', 'updated_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_contract_detail_contract_id',
			                     '{{%contract_detail}}', 'contract_id',
			                     '{{%contract}}', 'id',
			                     'CASCADE', 'CASCADE'
			);
			$this->addForeignKey('fk_contract_detail_part_id',
			                     '{{%contract_detail}}', 'part_id',
			                     '{{%part}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_crushing_created_by',
			                     '{{%crushing}}', 'created_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_crushing_part_id',
			                     '{{%crushing}}', 'part_id',
			                     '{{%part}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_crushing_updated_by',
			                     '{{%crushing}}', 'updated_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_document_created_by',
			                     '{{%document}}', 'created_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_document_document_type_id',
			                     '{{%document}}', 'document_type_id',
			                     '{{%document_type}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_document_from_warehouse_id',
			                     '{{%document}}', 'from_warehouse_id',
			                     '{{%warehouse}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_document_to_warehouse_id',
			                     '{{%document}}', 'to_warehouse_id',
			                     '{{%warehouse}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_document_updated_by',
			                     '{{%document}}', 'updated_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_document_supplier_id',
			                     '{{%document}}', 'supplier_id',
			                     '{{%supplier}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_document_detail_document_id',
			                     '{{%document_detail}}', 'document_id',
			                     '{{%document}}', 'id',
			                     'CASCADE', 'CASCADE'
			);
			$this->addForeignKey('fk_document_detail_part_id',
			                     '{{%document_detail}}', 'part_id',
			                     '{{%part}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_document_detail_sub_document_id',
			                     '{{%document_detail_sub}}', 'document_id',
			                     '{{%document}}', 'id',
			                     'CASCADE', 'CASCADE'
			);
			$this->addForeignKey('fk_document_detail_sub_part_id',
			                     '{{%document_detail_sub}}', 'part_id',
			                     '{{%part}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_document_detail_sub_sub_part_id',
			                     '{{%document_detail_sub}}', 'sub_part_id',
			                     '{{%part}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_document_detail_sub_warehouse_id',
			                     '{{%document_detail_sub}}', 'warehouse_id',
			                     '{{%warehouse}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_document_type_created_by',
			                     '{{%document_type}}', 'created_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_document_type_updated_by',
			                     '{{%document_type}}', 'updated_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_history_document_detail_history_document_id',
			                     '{{%history_document_detail}}', 'history_document_id',
			                     '{{%history_document}}', 'id',
			                     'CASCADE', 'CASCADE'
			);
			$this->addForeignKey('fk_invoice_created_by',
			                     '{{%invoice}}', 'created_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_invoice_supplier_id',
			                     '{{%invoice}}', 'supplier_id',
			                     '{{%supplier}}', 'id',
			                     'SET NULL', 'RESTRICT'
			);
			$this->addForeignKey('fk_invoice_updated_by',
			                     '{{%invoice}}', 'updated_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_invoice_detail_created_by',
			                     '{{%invoice_detail}}', 'created_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_invoice_detail_updated_by',
			                     '{{%invoice_detail}}', 'updated_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_invoice_detail_contract_id',
			                     '{{%invoice_detail}}', 'contract_id',
			                     '{{%contract}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_invoice_detail_cont_inv_id',
			                     '{{%invoice_detail}}', 'cont_inv_id',
			                     '{{%container_invoice}}', 'id',
			                     'CASCADE', 'RESTRICT'
			);
			$this->addForeignKey('fk_invoice_detail_part_id',
			                     '{{%invoice_detail}}', 'part_id',
			                     '{{%part}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_invoice_detail_part_order_id',
			                     '{{%invoice_detail}}', 'part_order_id',
			                     '{{%part_order}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_lc_contract_id',
			                     '{{%lc}}', 'contract_id',
			                     '{{%contract}}', 'id',
			                     'SET NULL', 'RESTRICT'
			);
			$this->addForeignKey('fk_lc_created_by',
			                     '{{%lc}}', 'created_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_lc_part_order_id',
			                     '{{%lc}}', 'part_order_id',
			                     '{{%part_order}}', 'id',
			                     'SET NULL', 'RESTRICT'
			);
			$this->addForeignKey('fk_lc_updated_by',
			                     '{{%lc}}', 'updated_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_lms_created_by',
			                     '{{%lms}}', 'created_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_lms_part_id',
			                     '{{%lms}}', 'part_id',
			                     '{{%part}}', 'id',
			                     'CASCADE', 'RESTRICT'
			);
			$this->addForeignKey('fk_lms_supplier_id',
			                     '{{%lms}}', 'supplier_id',
			                     '{{%supplier}}', 'id',
			                     'SET NULL', 'RESTRICT'
			);
			$this->addForeignKey('fk_lms_updated_by',
			                     '{{%lms}}', 'updated_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_lms_warehouse_id',
			                     '{{%lms}}', 'warehouse_id',
			                     '{{%warehouse}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_mfu_consolidation_type_id',
			                     '{{%mfu}}', 'consolidation_type_id',
			                     '{{%consolidation_type}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_mfu_contract_source_id',
			                     '{{%mfu}}', 'contract_source_id',
			                     '{{%contract_source}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_mfu_created_by',
			                     '{{%mfu}}', 'created_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_mfu_part_id',
			                     '{{%mfu}}', 'part_id',
			                     '{{%part}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_mfu_ship_mode_id',
			                     '{{%mfu}}', 'ship_mode_id',
			                     '{{%ship_mode}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_mfu_updated_by',
			                     '{{%mfu}}', 'updated_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_pack_created_by',
			                     '{{%pack}}', 'created_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_pack_part_id',
			                     '{{%pack}}', 'part_id',
			                     '{{%part}}', 'id',
			                     'CASCADE', 'RESTRICT'
			);
			$this->addForeignKey('fk_pack_product_line_id',
			                     '{{%pack}}', 'product_line_id',
			                     '{{%product_line}}', 'id',
			                     'SET NULL', 'RESTRICT'
			);
			$this->addForeignKey('fk_pack_product_model_id',
			                     '{{%pack}}', 'product_model_id',
			                     '{{%product_model}}', 'id',
			                     'SET NULL', 'RESTRICT'
			);
			$this->addForeignKey('fk_pack_supplier_id',
			                     '{{%pack}}', 'supplier_id',
			                     '{{%supplier}}', 'id',
			                     'SET NULL', 'RESTRICT'
			);
			$this->addForeignKey('fk_pack_updated_by',
			                     '{{%pack}}', 'updated_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_pack_warehouse_id',
			                     '{{%pack}}', 'warehouse_id',
			                     '{{%warehouse}}', 'id',
			                     'SET NULL', 'RESTRICT'
			);
			$this->addForeignKey('fk_part_created_by',
			                     '{{%part}}', 'created_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_part_unit_id',
			                     '{{%part}}', 'unit_id',
			                     '{{%unit}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_part_updated_by',
			                     '{{%part}}', 'updated_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_part_contract_source_id',
			                     '{{%part}}', 'contract_source_id',
			                     '{{%contract_source}}', 'id',
			                     'SET NULL', 'RESTRICT'
			);
			$this->addForeignKey('fk_part_warehouse_id',
			                     '{{%part}}', 'warehouse_id',
			                     '{{%warehouse}}', 'id',
			                     'CASCADE', 'RESTRICT'
			);
			$this->addForeignKey('fk_part_order_contract_id',
			                     '{{%part_order}}', 'contract_id',
			                     '{{%contract}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_part_order_created_by',
			                     '{{%part_order}}', 'created_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_part_order_updated_by',
			                     '{{%part_order}}', 'updated_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_part_order_detail_created_by',
			                     '{{%part_order_detail}}', 'created_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_part_order_detail_part_id',
			                     '{{%part_order_detail}}', 'part_id',
			                     '{{%part}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_part_order_detail_part_order_id',
			                     '{{%part_order_detail}}', 'part_order_id',
			                     '{{%part_order}}', 'id',
			                     'CASCADE', 'RESTRICT'
			);
			$this->addForeignKey('fk_part_order_detail_updated_by',
			                     '{{%part_order_detail}}', 'updated_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_part_part_created_by',
			                     '{{%part_part}}', 'created_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_part_part_part_id',
			                     '{{%part_part}}', 'part_id',
			                     '{{%part}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_part_part_sub_part_id',
			                     '{{%part_part}}', 'sub_part_id',
			                     '{{%part}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_part_part_updated_by',
			                     '{{%part_part}}', 'updated_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_part_part_warehouse_id',
			                     '{{%part_part}}', 'warehouse_id',
			                     '{{%warehouse}}', 'id',
			                     'CASCADE', 'RESTRICT'
			);
			$this->addForeignKey('fk_part_part_wide_part_id',
			                     '{{%part_part_wide}}', 'part_id',
			                     '{{%part}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_part_part_wide_sub_part_id',
			                     '{{%part_part_wide}}', 'sub_part_id',
			                     '{{%part}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_part_part_wide_warehouse_id',
			                     '{{%part_part_wide}}', 'warehouse_id',
			                     '{{%warehouse}}', 'id',
			                     'CASCADE', 'RESTRICT'
			);
			$this->addForeignKey('fk_product_parts_part_id',
			                     '{{%product_parts}}', 'part_id',
			                     '{{%part}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_product_parts_warehouse_id',
			                     '{{%product_parts}}', 'warehouse_id',
			                     '{{%warehouse}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_production_order_created_by',
			                     '{{%production_order}}', 'created_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_production_order_part_id',
			                     '{{%production_order}}', 'part_id',
			                     '{{%part}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_production_order_updated_by',
			                     '{{%production_order}}', 'updated_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_production_order_defect_created_by',
			                     '{{%production_order_defect}}', 'created_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_production_order_defect_defect_id',
			                     '{{%production_order_defect}}', 'defect_id',
			                     '{{%defect}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_production_order_defect_production_order_id',
			                     '{{%production_order_defect}}', 'production_order_id',
			                     '{{%production_order}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_production_order_history_created_by',
			                     '{{%production_order_history}}', 'created_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_production_order_history_production_order_id',
			                     '{{%production_order_history}}', 'production_order_id',
			                     '{{%production_order}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_production_order_sub_production_order_id',
			                     '{{%production_order_sub}}', 'production_order_id',
			                     '{{%production_order}}', 'id',
			                     'CASCADE', 'CASCADE'
			);
			$this->addForeignKey('fk_production_order_sub_sub_part_id',
			                     '{{%production_order_sub}}', 'sub_part_id',
			                     '{{%part}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_production_order_sub_warehouse_id',
			                     '{{%production_order_sub}}', 'warehouse_id',
			                     '{{%warehouse}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_production_plan_part_id',
			                     '{{%production_plan}}', 'part_id',
			                     '{{%part}}', 'id',
			                     'CASCADE', 'CASCADE'
			);
			$this->addForeignKey('fk_production_plan_warehouse_id',
			                     '{{%production_plan}}', 'warehouse_id',
			                     '{{%warehouse}}', 'id',
			                     'CASCADE', 'CASCADE'
			);
			$this->addForeignKey('fk_report_created_by',
			                     '{{%report}}', 'created_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_report_updated_by',
			                     '{{%report}}', 'updated_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_req_part_id',
			                     '{{%req}}', 'part_id',
			                     '{{%part}}', 'id',
			                     'CASCADE', 'CASCADE'
			);
			$this->addForeignKey('fk_req_detail_wide_req_id',
			                     '{{%req_detail_wide}}', 'req_id',
			                     '{{%req}}', 'id',
			                     'CASCADE', 'CASCADE'
			);
			$this->addForeignKey('fk_stock_part_id',
			                     '{{%stock}}', 'part_id',
			                     '{{%part}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_stock_warehouse_id',
			                     '{{%stock}}', 'warehouse_id',
			                     '{{%warehouse}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_user_report_report_id',
			                     '{{%user_report}}', 'report_id',
			                     '{{%report}}', 'id',
			                     'CASCADE', 'CASCADE'
			);
			$this->addForeignKey('fk_user_report_user_id',
			                     '{{%user_report}}', 'user_id',
			                     '{{%user}}', 'id',
			                     'CASCADE', 'CASCADE'
			);
			$this->addForeignKey('fk_user_warehouse_user_id',
			                     '{{%user_warehouse}}', 'user_id',
			                     '{{%user}}', 'id',
			                     'CASCADE', 'CASCADE'
			);
			$this->addForeignKey('fk_user_warehouse_warehouse_id',
			                     '{{%user_warehouse}}', 'warehouse_id',
			                     '{{%warehouse}}', 'id',
			                     'CASCADE', 'CASCADE'
			);
			$this->addForeignKey('fk_warehouse_created_by',
			                     '{{%warehouse}}', 'created_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
			$this->addForeignKey('fk_warehouse_updated_by',
			                     '{{%warehouse}}', 'updated_by',
			                     '{{%user}}', 'id',
			                     'RESTRICT', 'RESTRICT'
			);
		}

		public function safeDown(){
			$this->dropForeignKey('fk_balance_created_by', '{{%balance}}');
			$this->dropForeignKey('fk_balance_part_id', '{{%balance}}');
			$this->dropForeignKey('fk_balance_warehouse_id', '{{%balance}}');
			$this->dropForeignKey('fk_container_created_by', '{{%container}}');
			$this->dropForeignKey('fk_container_updated_by', '{{%container}}');
			$this->dropForeignKey('fk_container_invoice_delivery_term_id', '{{%container_invoice}}');
			$this->dropForeignKey('fk_container_invoice_arrived_by', '{{%container_invoice}}');
			$this->dropForeignKey('fk_container_invoice_container_id', '{{%container_invoice}}');
			$this->dropForeignKey('fk_container_invoice_document_id', '{{%container_invoice}}');
			$this->dropForeignKey('fk_container_invoice_invoice_id', '{{%container_invoice}}');
			$this->dropForeignKey('fk_container_invoice_received_by', '{{%container_invoice}}');
			$this->dropForeignKey('fk_container_invoice_ship_mode_id', '{{%container_invoice}}');
			$this->dropForeignKey('fk_container_invoice_shipped_by', '{{%container_invoice}}');
			$this->dropForeignKey('fk_contract_buyer_id', '{{%contract}}');
			$this->dropForeignKey('fk_contract_contract_source_id', '{{%contract}}');
			$this->dropForeignKey('fk_contract_contract_subject_id', '{{%contract}}');
			$this->dropForeignKey('fk_contract_created_by', '{{%contract}}');
			$this->dropForeignKey('fk_contract_currency_id', '{{%contract}}');
			$this->dropForeignKey('fk_contract_delivery_term_id', '{{%contract}}');
			$this->dropForeignKey('fk_contract_payment_term_id', '{{%contract}}');
			$this->dropForeignKey('fk_contract_supplier_id', '{{%contract}}');
			$this->dropForeignKey('fk_contract_updated_by', '{{%contract}}');
			$this->dropForeignKey('fk_contract_detail_contract_id', '{{%contract_detail}}');
			$this->dropForeignKey('fk_contract_detail_part_id', '{{%contract_detail}}');
			$this->dropForeignKey('fk_crushing_created_by', '{{%crushing}}');
			$this->dropForeignKey('fk_crushing_part_id', '{{%crushing}}');
			$this->dropForeignKey('fk_crushing_updated_by', '{{%crushing}}');
			$this->dropForeignKey('fk_delivery_plan_product_id', '{{%delivery_plan}}');
			$this->dropForeignKey('fk_document_created_by', '{{%document}}');
			$this->dropForeignKey('fk_document_document_type_id', '{{%document}}');
			$this->dropForeignKey('fk_document_from_warehouse_id', '{{%document}}');
			$this->dropForeignKey('fk_document_to_warehouse_id', '{{%document}}');
			$this->dropForeignKey('fk_document_updated_by', '{{%document}}');
			$this->dropForeignKey('fk_document_supplier_id', '{{%document}}');
			$this->dropForeignKey('fk_document_detail_document_id', '{{%document_detail}}');
			$this->dropForeignKey('fk_document_detail_part_id', '{{%document_detail}}');
			$this->dropForeignKey('fk_document_detail_sub_document_id', '{{%document_detail_sub}}');
			$this->dropForeignKey('fk_document_detail_sub_part_id', '{{%document_detail_sub}}');
			$this->dropForeignKey('fk_document_detail_sub_sub_part_id', '{{%document_detail_sub}}');
			$this->dropForeignKey('fk_document_detail_sub_warehouse_id', '{{%document_detail_sub}}');
			$this->dropForeignKey('fk_document_type_created_by', '{{%document_type}}');
			$this->dropForeignKey('fk_document_type_updated_by', '{{%document_type}}');
			$this->dropForeignKey('fk_history_document_detail_history_document_id', '{{%history_document_detail}}');
			$this->dropForeignKey('fk_invoice_created_by', '{{%invoice}}');
			$this->dropForeignKey('fk_invoice_supplier_id', '{{%invoice}}');
			$this->dropForeignKey('fk_invoice_updated_by', '{{%invoice}}');
			$this->dropForeignKey('fk_invoice_detail_created_by', '{{%invoice_detail}}');
			$this->dropForeignKey('fk_invoice_detail_updated_by', '{{%invoice_detail}}');
			$this->dropForeignKey('fk_invoice_detail_contract_id', '{{%invoice_detail}}');
			$this->dropForeignKey('fk_invoice_detail_cont_inv_id', '{{%invoice_detail}}');
			$this->dropForeignKey('fk_invoice_detail_part_id', '{{%invoice_detail}}');
			$this->dropForeignKey('fk_invoice_detail_part_order_id', '{{%invoice_detail}}');
			$this->dropForeignKey('fk_lc_contract_id', '{{%lc}}');
			$this->dropForeignKey('fk_lc_created_by', '{{%lc}}');
			$this->dropForeignKey('fk_lc_part_order_id', '{{%lc}}');
			$this->dropForeignKey('fk_lc_updated_by', '{{%lc}}');
			$this->dropForeignKey('fk_lms_created_by', '{{%lms}}');
			$this->dropForeignKey('fk_lms_part_id', '{{%lms}}');
			$this->dropForeignKey('fk_lms_supplier_id', '{{%lms}}');
			$this->dropForeignKey('fk_lms_updated_by', '{{%lms}}');
			$this->dropForeignKey('fk_lms_warehouse_id', '{{%lms}}');
			$this->dropForeignKey('fk_mfu_consolidation_type_id', '{{%mfu}}');
			$this->dropForeignKey('fk_mfu_contract_source_id', '{{%mfu}}');
			$this->dropForeignKey('fk_mfu_created_by', '{{%mfu}}');
			$this->dropForeignKey('fk_mfu_part_id', '{{%mfu}}');
			$this->dropForeignKey('fk_mfu_ship_mode_id', '{{%mfu}}');
			$this->dropForeignKey('fk_mfu_updated_by', '{{%mfu}}');
			$this->dropForeignKey('fk_pack_created_by', '{{%pack}}');
			$this->dropForeignKey('fk_pack_part_id', '{{%pack}}');
			$this->dropForeignKey('fk_pack_product_line_id', '{{%pack}}');
			$this->dropForeignKey('fk_pack_product_model_id', '{{%pack}}');
			$this->dropForeignKey('fk_pack_supplier_id', '{{%pack}}');
			$this->dropForeignKey('fk_pack_updated_by', '{{%pack}}');
			$this->dropForeignKey('fk_pack_warehouse_id', '{{%pack}}');
			$this->dropForeignKey('fk_part_created_by', '{{%part}}');
			$this->dropForeignKey('fk_part_unit_id', '{{%part}}');
			$this->dropForeignKey('fk_part_updated_by', '{{%part}}');
			$this->dropForeignKey('fk_part_contract_source_id', '{{%part}}');
			$this->dropForeignKey('fk_part_warehouse_id', '{{%part}}');
			$this->dropForeignKey('fk_part_order_contract_id', '{{%part_order}}');
			$this->dropForeignKey('fk_part_order_created_by', '{{%part_order}}');
			$this->dropForeignKey('fk_part_order_updated_by', '{{%part_order}}');
			$this->dropForeignKey('fk_part_order_detail_created_by', '{{%part_order_detail}}');
			$this->dropForeignKey('fk_part_order_detail_part_id', '{{%part_order_detail}}');
			$this->dropForeignKey('fk_part_order_detail_part_order_id', '{{%part_order_detail}}');
			$this->dropForeignKey('fk_part_order_detail_updated_by', '{{%part_order_detail}}');
			$this->dropForeignKey('fk_part_part_created_by', '{{%part_part}}');
			$this->dropForeignKey('fk_part_part_part_id', '{{%part_part}}');
			$this->dropForeignKey('fk_part_part_sub_part_id', '{{%part_part}}');
			$this->dropForeignKey('fk_part_part_updated_by', '{{%part_part}}');
			$this->dropForeignKey('fk_part_part_warehouse_id', '{{%part_part}}');
			$this->dropForeignKey('fk_part_part_wide_part_id', '{{%part_part_wide}}');
			$this->dropForeignKey('fk_part_part_wide_sub_part_id', '{{%part_part_wide}}');
			$this->dropForeignKey('fk_part_part_wide_warehouse_id', '{{%part_part_wide}}');
			$this->dropForeignKey('fk_product_parts_part_id', '{{%product_parts}}');
			$this->dropForeignKey('fk_product_parts_product_id', '{{%product_parts}}');
			$this->dropForeignKey('fk_product_parts_warehouse_id', '{{%product_parts}}');
			$this->dropForeignKey('fk_production_order_created_by', '{{%production_order}}');
			$this->dropForeignKey('fk_production_order_part_id', '{{%production_order}}');
			$this->dropForeignKey('fk_production_order_updated_by', '{{%production_order}}');
			$this->dropForeignKey('fk_production_order_defect_created_by', '{{%production_order_defect}}');
			$this->dropForeignKey('fk_production_order_defect_defect_id', '{{%production_order_defect}}');
			$this->dropForeignKey('fk_production_order_defect_production_order_id', '{{%production_order_defect}}');
			$this->dropForeignKey('fk_production_order_history_created_by', '{{%production_order_history}}');
			$this->dropForeignKey('fk_production_order_history_production_order_id', '{{%production_order_history}}');
			$this->dropForeignKey('fk_production_order_sub_production_order_id', '{{%production_order_sub}}');
			$this->dropForeignKey('fk_production_order_sub_sub_part_id', '{{%production_order_sub}}');
			$this->dropForeignKey('fk_production_order_sub_warehouse_id', '{{%production_order_sub}}');
			$this->dropForeignKey('fk_production_plan_part_id', '{{%production_plan}}');
			$this->dropForeignKey('fk_production_plan_warehouse_id', '{{%production_plan}}');
			$this->dropForeignKey('fk_report_created_by', '{{%report}}');
			$this->dropForeignKey('fk_report_updated_by', '{{%report}}');
			$this->dropForeignKey('fk_req_part_id', '{{%req}}');
			$this->dropForeignKey('fk_req_detail_wide_req_id', '{{%req_detail_wide}}');
			$this->dropForeignKey('fk_stock_part_id', '{{%stock}}');
			$this->dropForeignKey('fk_stock_warehouse_id', '{{%stock}}');
			$this->dropForeignKey('fk_user_report_report_id', '{{%user_report}}');
			$this->dropForeignKey('fk_user_report_user_id', '{{%user_report}}');
			$this->dropForeignKey('fk_user_warehouse_user_id', '{{%user_warehouse}}');
			$this->dropForeignKey('fk_user_warehouse_warehouse_id', '{{%user_warehouse}}');
			$this->dropForeignKey('fk_warehouse_created_by', '{{%warehouse}}');
			$this->dropForeignKey('fk_warehouse_updated_by', '{{%warehouse}}');
		}
	}
