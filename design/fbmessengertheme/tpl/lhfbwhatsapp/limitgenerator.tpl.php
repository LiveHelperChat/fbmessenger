<?php
$instance = \LiveHelperChatExtension\fbmessenger\providers\FBMessengerWhatsAppLiveHelperChat::getInstance();
$phones = $instance->getPhones();
?>

<div>
<h5><label><input type="checkbox" name="business_account[]" value="default"> Default</label></h5>
<?php foreach ($phones as $phone)  : ?>
    <label class="d-block"><input type="checkbox" name="phone_id[]" value="<?php echo htmlspecialchars($phone['id'])?>"> <?php echo $phone['display_phone_number'],' | ', $phone['verified_name'],' | ', $phone['code_verification_status'],' | ', $phone['quality_rating']?></label>
<?php endforeach; ?>
</div>

<?php foreach (\LiveHelperChatExtension\fbmessenger\providers\erLhcoreClassModelMessageFBWhatsAppAccount::getList(['filter' => ['active' => 1]]) as $businessAccount) :
    $instance->setAccessToken($businessAccount->access_token);
    $instance->setBusinessAccountID($businessAccount->business_account_id);
 ?>
<div>
    <h5><label><input type="checkbox" name="business_account[]" value="<?php echo htmlspecialchars($businessAccount->id)?>"> <?php echo htmlspecialchars($businessAccount->name)?></label></h5>
<?php foreach ($instance->getPhones() as $phone)  : ?>
    <label class="d-block"><input type="checkbox" name="phone_id[]" value="<?php echo htmlspecialchars($phone['id'])?>"> <?php echo $phone['display_phone_number'],' | ', $phone['verified_name'],' | ', $phone['code_verification_status'],' | ', $phone['quality_rating']?></label>
<?php endforeach; ?>
</div>
<?php endforeach;?>

Permission to copy to Limitation section of role.

<textarea class="form-control" id="permission-roles" rows="5" readonly placeholder="<?php echo htmlspecialchars('E.g {"phones":["233359883191400"],"business_accounts":[3,"default"]}');?>"></textarea>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const permissionRolesTextarea = document.getElementById('permission-roles');
        const phoneCheckboxes = document.querySelectorAll('input[name="phone_id[]"]');
        const businessAccountCheckboxes = document.querySelectorAll('input[name="business_account[]"]');

        function updatePermissionRoles() {
            const selectedPhones = Array.from(phoneCheckboxes)
                .filter(checkbox => checkbox.checked)
                .map(checkbox => checkbox.value);

            const selectedBusinessAccounts = Array.from(businessAccountCheckboxes)
                .filter(checkbox => checkbox.checked)
                .map(checkbox => checkbox.value);

            const data = {
                phones: selectedPhones,
                business_accounts: selectedBusinessAccounts
            };

            permissionRolesTextarea.value = JSON.stringify(data, null, 2);
        }

        function syncGroupCheckboxes() {
            businessAccountCheckboxes.forEach(groupCheckbox => {
                const groupId = groupCheckbox.value;
                const groupPhones = Array.from(phoneCheckboxes).filter(phoneCheckbox => {
                    return phoneCheckbox.closest('div').querySelector(`input[name="business_account[]"][value="${groupId}"]`);
                });

                // Check group checkbox if any phone in the group is selected
                const anyPhoneSelected = groupPhones.some(phoneCheckbox => phoneCheckbox.checked);
                groupCheckbox.checked = anyPhoneSelected;
            });
        }

        phoneCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                syncGroupCheckboxes();
                updatePermissionRoles();
            });
        });

        businessAccountCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updatePermissionRoles);
        });
    });
</script>