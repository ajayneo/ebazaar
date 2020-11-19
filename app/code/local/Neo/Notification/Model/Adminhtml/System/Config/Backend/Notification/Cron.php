<?php
class Neo_Notification_Model_Adminhtml_System_Config_Backend_Notification_Cron extends Mage_Core_Model_Config_Data
{
    const CRON_STRING_PATH = 'crontab/jobs/notification_cron_eb/schedule/cron_expr';
    const CRON_MODEL_PATH = 'crontab/jobs/notification_cron_eb/run/model';

    protected function _afterSave()
    {
        $time = $this->getData('groups/neo_notification/fields/time/value');
        $frequncy = $this->getData('groups/neo_notification/fields/frequency/value');

        $frequencyDaily = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_DAILY;
        $frequencyWeekly = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_WEEKLY;
        $frequencyMonthly = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_MONTHLY;

        $cronDayOfWeek = date('N');

        $cronExprArray = array(
            intval($time[1]),                                   # Minute
            intval($time[0]),                                   # Hour
            (frequency == $frequencyMonthly) ? '1' : '*',       # Day of the Month
            '*',                                                # Month of the Year
            (frequency == $frequencyWeekly) ? '1' : '*',        # Day of the Week
        );
        $cronExprString = join(' ', $cronExprArray);

        try {
            Mage::getModel('core/config_data')
                ->load(self::CRON_STRING_PATH, 'path')
                ->setValue($cronExprString)
                ->setPath(self::CRON_STRING_PATH)
                ->save();
            Mage::getModel('core/config_data')
                ->load(self::CRON_MODEL_PATH, 'path')
                ->setValue((string) Mage::getConfig()->getNode(self::CRON_MODEL_PATH))
                ->setPath(self::CRON_MODEL_PATH)
                ->save();
            $timecreated = strftime("%Y-%m-%d %H:%M:%S",  mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")));
            $timescheduled = strftime("%Y-%m-%d %H:%M:%S", mktime(date("H"), date("i")+ 5, date("s"), date("m"), date("d"), date("Y")));
            $jobCode = 'notification_trigger_cron_eb';
            $schedule = Mage::getModel('cron/schedule')->load($jobCode, 'job_code')
                        ->setJobCode($jobCode)
                        ->setCreatedAt($timecreated)
                        ->setScheduledAt($timescheduled)
                        ->setStatus(Mage_Cron_Model_Schedule::STATUS_PENDING)
                        ->save();
            //$schedule->setJobCode($jobCode)
        }
        catch (Exception $e) {
            throw new Exception(Mage::helper('cron')->__('Unable to save the cron expression.'));
        }
    }
}
?>