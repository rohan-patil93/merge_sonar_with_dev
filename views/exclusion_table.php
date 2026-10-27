<table class="table table-separate table-head-custom table-checkable" id="kt_datatable1">
        <tr>
          <th scope="col">#.</th>
         <th scope="col">Employee Code</th>

           <th scope="col">Name</th>
           <th scope="col"></th>
        </tr>
          <?php foreach($data as $dd){?>
          <tr>
           <td>
              <?php echo $dd['name']; ?>
           </td>
           <td><?php echo $dd['name']; ?></td>
           <td>

           </td>

           </tr>
           <?php } ?>
    </table>
