<?php

Functions::Check_User_Permissions_Redirect('Staff');
$View = Functions::Get_View();

switch ($_POST['Mode'])
    {
        case 'Edit':
            $User = new User($_POST['userID']);
            $User->Edit_User(Functions::Make_Safe($_POST['First_Name']), Functions::Make_Safe($_POST['Last_Name']), Functions::Make_Safe($_POST['EMail']), Functions::Make_Safe($_POST['Permissions']), Functions::Make_Safe($_POST['Password']));
            $User->Display_Message();
            break;
        case 'Search by Username':
            $Connection = new Connection();
            $User_Array = array (':Username'=>$_POST['SearchUsername']);
            $User_Results = $Connection->Custom_Query("SELECT * from users WHERE Username=:Username", $User_Array);
            break;
        case 'Search by First':
            $Connection = new Connection();
            $User_Array = array (':First_Name'=>$_POST['SearchFirstName']);
            $User_Results = $Connection->Custom_Query("SELECT * from users WHERE First_Name=:First_Name", $User_Array);
            break;
        case 'Search by Last':
            $Connection = new Connection();
            $User_Array = array (':Last_Name'=>$_POST['SearchLastName']);
            $User_Results = $Connection->Custom_Query("SELECT * from users WHERE Last_Name=:Last_Name", $User_Array);
            break;
    }

echo "<div class='ContentHeader'>Edit Users</div>";
echo "<hr>";

echo "<div class='ContentHeader'>Search by Username</div><hr>";
echo "
<form action='?view={$View}' method='post'>
        <table>
            <tr>
                <td>
                    <input name='SearchUsername' value=''>
                </td>
                <td>
                    <input type='submit' value='Search by Username' name='Mode'>
                </td>
            </tr>
        </table>
    </form>
";

echo "<div class='ContentHeader'>Search by First Name</div><hr>";
echo "
<form action='?view={$View}' method='post'>
        <table>
            <tr>
                <td>
                    <input name='SearchFirstName' value=''>
                </td>
                <td>
                    <input type='submit' value='Search by First' name='Mode'>
                </td>
            </tr>
        </table>
    </form>
";

echo "<div class='ContentHeader'>Search by Last Name</div><hr>";
echo "
<form action='?view={$View}' method='post'>
        <table>
            <tr>
                <td>
                    <input name='SearchLastName' value=''>
                </td>
                <td>
                    <input type='submit' value='Search by Last' name='Mode'>
                </td>
            </tr>
        </table>
    </form>
";


if ($_POST['Mode'] = 'Search')
{
    if ($User_Results)
    {
        //Front end to Edit or Delete a blog entry.
    echo "<div class='ContentHeader'>Editing user {$User_Results['Username']}</div><hr>";
    echo"
    <div class='BlogWrapper'>
    <form action='?view=edit_user' method='post'>
        <table>
            <tr>
                <td>
                Username:
                </td>
                <td>
                    <input size='50' type='text' value='{$User_Results['Username']}' name='Username' disabled>
                </td>
            </tr>
            <tr>
                <td>
                First Name:
                </td>
                <td>
                    <input size='50' type='text' value='{$User_Results['First_Name']}' name='First_Name'>
                </td>
            </tr>
            <tr>
                <td>
                Last Name:
                </td>
                <td>
                    <input size='50' type='text' value='{$User_Results['Last_Name']}' name='Last_Name'>
                </td>
            </tr>
            <tr>
                <td>
                Password:
                </td>
                <td>
                    <input size='50' type='text' value='' name='Password'>
                </td>
            </tr>
            <tr>
                <td>
                Email:
                </td>
                <td>
                    <input size='50' type='text' value='{$User_Results['EMail']}' name='EMail'>
                </td>
            </tr>
                <tr>
                <td>
                Permissions:
                </td>
                <td>
                    <input size='50' type='text' value='{$User_Results['Permissions']}' name='Permissions'>
                </td>
            </tr>
            <tr>
                <td>
                    <input name='userID' type='hidden' value='{$User_Results['ID']}'>
                </td>
            </tr>
            <tr>
                <td>
                    <input size='10' type='submit' value='Edit' name='Mode'>
                </td>
            </tr>
        </table>
    </form>
    </div>
    <br>
    ";
    } else {
    echo "No users found, please refine your search and try again.";
    }
}
