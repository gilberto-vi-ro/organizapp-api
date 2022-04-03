<?php

?>
<!DOCTYPE html>
<html>

<head>

    <title>Prueba</title>

    <meta charset="UTF-8">
    <meta http-equiv="pragma" content="no-cache" />
    <meta name="viewport" content="width-device-width, user-scalable=no, initial-scale=1.0, maiximum-scale1.0, minimum-scale=1.0">

    <!-- ===============================================================================
    JS
    =================================================================================-->

    <script src="jquery.min.3.3.1.js"></script>

  	<script type="text/javascript">
  		
	/*DEFAULT HomeController*/
		// let param1 = "1001";
		// $.get("http://127.0.0.1/program/organizapp-api/",{'id_user': param1}
		// 	,'json').done(function(response){
		// 		response = JSON.parse(response);
		// 		console.log(response);
		// });

	/** ===============================================================================================
	 * ADMIN CONTROLLER
	 * ================================================================================================*/
		/*DEFAULT*/
		// let param1 = "10028";
		// $.get("http://127.0.0.1/program/organizapp-api/AdminController",{ 'id_user': param1}
		// 	,'json').done(function(response){
		// 		console.log(response);
		// });
	
		/*LIST USERS*/
		// $.get("http://127.0.0.1/program/organizapp-api/AdminController/listUsers",{'list': 1}
		// 	,'json').done(function(response){
		// 		console.log(response);
		// });


		/*DELETE USER*/
		// let id = "10028";
		// $.get("http://127.0.0.1/program/organizapp-api/AdminController/deleteUser/", {'id_user': id}
		// 	,'json').done(function(response){
		// 		console.log(response);
		// });

	/** ===============================================================================================
	 * EDIT PROFILE CONTROLLER
	 * ================================================================================================*/
		
		/*UPDATE PROFILE*/
		// let param1 = "1001";
		// let param2 = "Nombre completo";
		// let param3 = "default";
		// let param4 = "0";
		// let file = this.files[0];
		// $.post("http://127.0.0.1/program/organizapp-api/EditProfileController/updateProfile",{'id_user':param1,"name_c": param2, "new_pwd": param3, "img_changed": param4, "img": file}
		// 	,'json').done(function(response){
		// 		console.log(response);
		// });

	/** ===============================================================================================
	 * FOLDER CONTROLLER
	 * ================================================================================================*/
	/*GET VALUES*/
	// let param1 = "1001";
	// 	$.get("http://127.0.0.1/program/organizapp-api/FolderController/getValues",{ 'id_user': param1}
	// 		,'json').done(function(response){
	// 			console.log(response);
	// });

	/*LIST FOLDER*/
	//  let param1 = "1002";
	//  let param2 = "drive/1002";
	//  $.get("http://127.0.0.1/program/organizapp-api/FolderController/listAll/",{"id_user": param1, 'pathname': param2}
	// 		,'json').done(function(response){
	// 			console.log(response);
	// });
	 
	/*LIST FOLDER SEARCH*/
	//  let param1 = "1002";
	//  let param2 = "drive/1002";
	//  let param3 = "hola";
	//  $.get("http://127.0.0.1/program/organizapp-api/FolderController/listAll/",{"id_user": param1, 'pathname': param2, "search": param3}
	// 		,'json').done(function(response){
	// 			console.log(response);
	// });

	/*SHOW FILE*/
	// let param1 = "1001";
	//  let param2 = "drive/1001/Captura de pantalla (220).png";
	//  let param3 = "png";
	//  $.get("http://127.0.0.1/program/organizapp-api/FolderController/showFile/",{"id_user": param1, 'pathname': param2, "extension": param3}
	// 		,'json').done(function(response){
	// 			console.log(response);
	// });

	/*DOWNLOAD FILE*/
	// let param1 = "1001";
	//  let param2 = "drive/1001/aa";
	//  let param3 = "1";
	//  $.get("http://127.0.0.1/program/organizapp-api/FolderController/download/",{"id_user": param1, 'pathname': param2, "is_dir": param3}
	// 		,'json').done(function(response){
	// 			console.log(response);
	// });

	/*CREATE FOLDER*/
	// let param1 = "1001";
	// let param2 = "drive/1001/";
	// let param3 = "default";
	// $.post("http://127.0.0.1/program/organizapp-api/FolderController/createFolder",{'id_user':param1,"pathname": param2,"name": param3, }
	// 	,'json').done(function(response){
	// 		console.log(response);
	// });

	/*RENAMED*/
	// let param1 = "1001";
	// let param2 = "drive/1001/renamed";
	// let param3 = "222";
	// $.post("http://127.0.0.1/program/organizapp-api/FolderController/rename",{'id_user':param1,"oldPathname": param2,"newName": param3, }
	// 	,'json').done(function(response){
	// 		console.log(response);
	// });

	/*MOVE*/
	// let param1 = "1001";
	// let param2 = "drive/1001/ingles";
	// let item = [
	// 		{
	// 			"name": "mate",
	// 			"path_name": "drive/1001/mate",
	// 		},
	// 		{
	// 			"name": "22",
	// 			"path_name": "drive/1001/22",
	// 		}
	// 	];
	// $.post("http://127.0.0.1/program/organizapp-api/FolderController/move",{'id_user':param1,"newPathname": param2,"item": item, }
	// 	,'json').done(function(response){
	// 		console.log(response);
	// });

	/*DELETE*/
	// let param1 = "1001";
	// let item = [
	// 		{
	// 			"path_name": "drive/1001/222.zip"
	// 		},
	// 		{
	// 			"path_name": "drive/1001/externalizar-software.jpg"
	// 		}
	// 	];
	// $.post("http://127.0.0.1/program/organizapp-api/FolderController/delete",{'id_user':param1,"item": item, }
	// 	,'json').done(function(response){
	// 		console.log(response);
	// });


	/*UPLOAD*/
	// let param1 = "1001";
	// let param2 = "drive/1001/";
	// let file = this.files[0];
	// $.post("http://127.0.0.1/program/organizapp-api/FolderController/upload",{'id_user':param1,'pathname':param2,"file_data": file, }
	// 	,'json').done(function(response){
	// 		console.log(response);
	// });

	/** ===============================================================================================
	 * HOME CONTROLLER
	 * ================================================================================================*/
  	
	 /*LIST FOLDER*/
	// let param1 = "1001";
	//  let param2 = "drive/1001/";
	//  $.get("http://127.0.0.1/program/organizapp-api/HomeController/listFolder/",{"id_user": param1, 'path': param2}
	// 		,'json').done(function(response){
	// 			console.log(response);
	// });


	 /*LIST FOLDER*/
	 let param1 = "1001";
	 let param2 = "drive/1001/";
	 let param3 = "1";
	 let param4 = "";
	 let param5 = "2022-03-15::2022-04-15";
	
	 $.get("http://127.0.0.1/program/organizapp-api/HomeController/listTaskPending/",{
		 "id_user": param1, 
		 'path': param2,
		 'priority': param3,
		 'search': param4,
		 'range': param5,
		},'json').done(function(response){
				response = JSON.parse(response);
				console.log(response);
	});

    // } else if (isset($_GET['listTaskPending'])) {
    //     $r = $HomeController->listTaskPending($_GET['path'], $_GET['priority'], $_GET['search'], $_GET['range']);
    //     //print_r($r);
    //     exit();
    // } else if (isset($_GET['listTaskDone'])) {
    //     $r = $HomeController->listTaskDone($_GET['path'], $_GET['priority'], $_GET['search'], $_GET['range']);
    //     //print_r($r);
    //     exit();
    // } else if (isset($_GET['listTaskDelivered'])) {
    //     $r = $HomeController->listTaskDelivered($_GET['path'], $_GET['priority'], $_GET['search'], $_GET['range']);
    //     //print_r($r);
    //     exit();
    // }
    // else if (isset($_GET['getPathFile'])) {
    //     $r = $HomeController->getPathFile($_GET['idFile']);
    //     exit();
    // }

    // if (isset($_POST['addNewTask'])) {
    //     $HomeController->addNewTask($_POST);
    //     //var_dump($_POST);
    //     exit();
    // }

	// else if (isset($_POST['editStatusTask']) ){
    //     $HomeController->editStatusTask($_POST);
    //     //var_dump($_POST);
    //     //var_dump($_FILES);
    //     exit();
    // }
    // else if (isset($_POST['editTask']) ){
    //     $HomeController->editTask($_POST);
    //     //var_dump($_POST);
    //     //var_dump($_FILES);
    //     exit();
    // }
    // else if (isset($_POST['deleteTask']) ){
    //     $HomeController->deleteTask( $_POST['item'] );
    //     exit();
    // }

	/** ===============================================================================================
	 * LOGIN CONTROLLER
	 * ================================================================================================*/
  		/*LOGIN*/
  		// let param1 = "g";
		// let param2 = "g";
		// $.post("http://127.0.0.1/program/organizapp-api/LoginController/login",{'username':param1,"pwd": param2 }
		// 	,'json').done(function(response){
		// 		//response = JSON.parse(response);
		// 		//console.log(response.response.msg);
		// 		console.log(response);
		// });

		/*REGISTER*/
		// let param1 = "g";
		// let param2 = "g";
		// let param3 = "g";
		// $.post("http://127.0.0.1/program/organizapp-api/LoginController/registerUser",{'username':param1,"nombre_c": param2, "pwd": param3 }
		// 	,'json').done(function(response){
		// 		console.log(response);
		// });

	/** ===============================================================================================
	 * TRASH CONTROLLER
	 * ================================================================================================*/
		/*GET VALUES*/
		// let param1 = "1001";
		// $.get("http://127.0.0.1/program/organizapp-api/TrashController/getValues",{ 'id_user': param1}
		// 	,'json').done(function(response){
		// 		console.log(response);
		// });
    

		/*LIST TRASH*/
		//  let param1 = "1001";
		//  let param2 = "drive/1001";
		//  $.get("http://127.0.0.1/program/organizapp-api/TrashController/listAllTrash/",{"id_user": param1, 'pathname': param2}
		// 		,'json').done(function(response){
		// 			console.log(response);
		// });

		/*LIST SEARCH TRASH*/
		// let param1 = "1001";
		// let param2 = "drive/1001";
		// let param3 = "22";
		// $.get("http://127.0.0.1/program/organizapp-api/TrashController/searchTrash/",{"id_user": param1, 'pathname': param2,'search': param3}
		// 		,'json').done(function(response){
		// 			console.log(response);
		// });


		/*RESTORE*/
		// let param1 = "1001";
		// let item = [
		// 		{
		// 			"path_name": "drive/1001/222.trash",
		// 		},
		// 		{
		// 			"path_name": "drive/1001/aa",
		// 		}
		// 	];
		// $.post("http://127.0.0.1/program/organizapp-api/TrashController/restoreTrash",{'id_user':param1,"item": item, }
		// 	,'json').done(function(response){
		// 		console.log(response);
		// });

		/*DELETE*/
		// let param1 = "1001";
		// let item = [
		// 		{
		// 			"path_name": "drive/1001/a.trash"
		// 		},
		// 		{
		// 			"path_name": "drive/1001/externalizar-software.jpg.trash"
		// 		}
		// 	];
		// $.post("http://127.0.0.1/program/organizapp-api/TrashController/deleteTrash",{'id_user':param1,"item": item, }
		// 	,'json').done(function(response){
		// 		console.log(response);
		// });

		
    </script>

</body>

</html>