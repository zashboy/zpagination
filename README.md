# ZPagination

## Simple bootstrap 4 pagination class with average number pagination and breadcrumbs, nothing fancy just does the job

### Usage example
```PHP
<?php
        $pagination = new Pagination;
        $pagination->setTotal(35);
        $pagination->setCurrentPage($_GET['currentpage']);
        $breadcrumbs = $pagination->showBreadCrumbs();
        $pagination_pages = $pagination->showPagination();
?>
```
```HTML
<html>
<head></head>
<body>
<div><?= $breadcrumbs ?></div>
<div>Great content...  </div>
<div><?= $pagination_pages ?></div>
</body>
</html>
```
