<?php defined('SYSPATH') or die('No direct script access.');

return array
(
   'vincode' => array
   (
      'url' => 'http://www.japancats.ru/',
      'marks' => array
      (
         'Mercedes' => array
         (
            'url' => 'http://www.elcats.ru/mercedes/CheckVin.aspx?VIN=:VINCODE',
         ),
         'Lexus' => array
         (
            'values' => '__EVENTTARGET=&__EVENTARGUMENT=&__LASTFOCUS=&__VIEWSTATE=%2FwEPDwUJODM1NjIxNjg1ZBgBBR5fX0NvbnRyb2xzUmVxdWlyZVBvc3RCYWNrS2V5X18WAgUhY3RsMDAkY3BoTWFzdGVyUGFnZSRjaGJTYXZlQ29va2llBSJjdGwwMCRjcGhNYXN0ZXJQYWdlJGNoYkZpbmRCeUZyYW1le75lwFhTFBgj%2F8xmymjv25JOtd8%3D&ctl00%24cphMasterPage%24txbVIN=:VINCODE&ctl00%24cphMasterPage%24btnFindByVIN=%D0%9F%D0%BE%D0%B8%D1%81%D0%BA&ctl00%24cphMasterPage%24txbFrame1=&ctl00%24cphMasterPage%24txbFrame2=&ctl00%24cphMasterPage%24rblCountry=E',
         ),
         'Honda' => array
         (
            'values' => '__EVENTTARGET=&__EVENTARGUMENT=&__VIEWSTATE=%2FwEPDwULLTEwNDA2NjUyOThkGAEFHl9fQ29udHJvbHNSZXF1aXJlUG9zdEJhY2tLZXlfXxYBBSFjdGwwMCRjcGhNYXN0ZXJQYWdlJGNoYlNhdmVDb29raWV66RMtO5JHvZq7nb8BzRx1mnK1TA%3D%3D&__EVENTVALIDATION=%2FwEWBALX5YOaBgKPmLHqDgLn0cGUCAKEwYK2BB0Vt7FUl3Mj%2FnJw9Xa0Lv1x3ZjS&ctl00%24cphMasterPage%24txbVIN=:VINCODE&ctl00%24cphMasterPage%24btnFindByVIN=%D0%9F%D0%BE%D0%B8%D1%81%D0%BA',
         ),
         'Mazda' => array
         (
            'values' => '__EVENTTARGET=&__EVENTARGUMENT=&__VIEWSTATE=%2FwEPDwULLTEwNDA2NjUyOThkGAEFHl9fQ29udHJvbHNSZXF1aXJlUG9zdEJhY2tLZXlfXxYBBSFjdGwwMCRjcGhNYXN0ZXJQYWdlJGNoYlNhdmVDb29raWViBfOS3b1vt0ep%2Ba%2Fts9jKPvRiPg%3D%3D&__EVENTVALIDATION=%2FwEWBAL48orDAQKPmLHqDgLn0cGUCAKEwYK2BAtDq1Wr%2FaUi0AGSEIxOP0I2hCJv&ctl00%24cphMasterPage%24txbVIN=:VINCODE&ctl00%24cphMasterPage%24btnFindByVIN=%D0%9F%D0%BE%D0%B8%D1%81%D0%BA',
         ),
         'Mitsubishi' => array // north america
         (
            'values' => '__EVENTTARGET=&__EVENTARGUMENT=&__LASTFOCUS=&__VIEWSTATE=%2FwEPDwUKLTE2NjAxMjE2MmQYAQUeX19Db250cm9sc1JlcXVpcmVQb3N0QmFja0tleV9fFgIFIWN0bDAwJGNwaE1hc3RlclBhZ2UkY2hiU2F2ZUNvb2tpZQUmY3RsMDAkY3BoTWFzdGVyUGFnZSRjaGJTYXZlQ29va2llRnJhbWWOelnG0HLGvYzgNFqGlCY9kC5Lhw%3D%3D&ctl00%24cphMasterPage%24rblRegionForVin=us&ctl00%24cphMasterPage%24txbVIN=:VINCODE&ctl00%24cphMasterPage%24btnFindByVIN=%D0%9F%D0%BE%D0%B8%D1%81%D0%BA&ctl00%24cphMasterPage%24txbFrame1=&ctl00%24cphMasterPage%24txbFrame2=&ctl00%24cphMasterPage%24rblCountry=eu',
         ),
         /*
         'Mitsubishi-eu' => array
         (
            'values' => '__EVENTTARGET=&__EVENTARGUMENT=&__LASTFOCUS=&__VIEWSTATE=%2FwEPDwUKLTE2NjAxMjE2MmQYAQUeX19Db250cm9sc1JlcXVpcmVQb3N0QmFja0tleV9fFgIFIWN0bDAwJGNwaE1hc3RlclBhZ2UkY2hiU2F2ZUNvb2tpZQUmY3RsMDAkY3BoTWFzdGVyUGFnZSRjaGJTYXZlQ29va2llRnJhbWWOelnG0HLGvYzgNFqGlCY9kC5Lhw%3D%3D&ctl00%24cphMasterPage%24rblRegionForVin=eu&ctl00%24cphMasterPage%24txbVIN=:VINCODE&ctl00%24cphMasterPage%24btnFindByVIN=%D0%9F%D0%BE%D0%B8%D1%81%D0%BA&ctl00%24cphMasterPage%24txbFrame1=&ctl00%24cphMasterPage%24txbFrame2=&ctl00%24cphMasterPage%24rblCountry=eu',
            'mark' => 'Mitsubishi',
         ),
         'Mitsubishi-sa' => array
         (
            'values' => '__EVENTTARGET=&__EVENTARGUMENT=&__LASTFOCUS=&__VIEWSTATE=%2FwEPDwUKLTE2NjAxMjE2MmQYAQUeX19Db250cm9sc1JlcXVpcmVQb3N0QmFja0tleV9fFgIFIWN0bDAwJGNwaE1hc3RlclBhZ2UkY2hiU2F2ZUNvb2tpZQUmY3RsMDAkY3BoTWFzdGVyUGFnZSRjaGJTYXZlQ29va2llRnJhbWWOelnG0HLGvYzgNFqGlCY9kC5Lhw%3D%3D&ctl00%24cphMasterPage%24rblRegionForVin=us&ctl00%24cphMasterPage%24txbVIN=:VINCODE&ctl00%24cphMasterPage%24btnFindByVIN=%D0%9F%D0%BE%D0%B8%D1%81%D0%BA&ctl00%24cphMasterPage%24txbFrame1=&ctl00%24cphMasterPage%24txbFrame2=&ctl00%24cphMasterPage%24rblCountry=eu',
            'mark' => 'Mitsubishi',
         ),
         'Mitsubishi-as' => array
         (
            'values' => '__EVENTTARGET=&__EVENTARGUMENT=&__LASTFOCUS=&__VIEWSTATE=%2FwEPDwUKLTE2NjAxMjE2MmQYAQUeX19Db250cm9sc1JlcXVpcmVQb3N0QmFja0tleV9fFgIFIWN0bDAwJGNwaE1hc3RlclBhZ2UkY2hiU2F2ZUNvb2tpZQUmY3RsMDAkY3BoTWFzdGVyUGFnZSRjaGJTYXZlQ29va2llRnJhbWWOelnG0HLGvYzgNFqGlCY9kC5Lhw%3D%3D&ctl00%24cphMasterPage%24rblRegionForVin=gl&ctl00%24cphMasterPage%24txbVIN=:VINCODE&ctl00%24cphMasterPage%24btnFindByVIN=%D0%9F%D0%BE%D0%B8%D1%81%D0%BA&ctl00%24cphMasterPage%24txbFrame1=&ctl00%24cphMasterPage%24txbFrame2=&ctl00%24cphMasterPage%24rblCountry=eu',
            'mark' => 'Mitsubishi',
         ),
         */
         'Nissan' => array
         (
            'values' => '__EVENTTARGET=&__EVENTARGUMENT=&__LASTFOCUS=&__VIEWSTATE=%2FwEPDwUJMjEyMjUyNzg0ZBgBBR5fX0NvbnRyb2xzUmVxdWlyZVBvc3RCYWNrS2V5X18WAQUhY3RsMDAkY3BoTWFzdGVyUGFnZSRjaGJTYXZlQ29va2llI3FiW1MXh7RpxF0olzwAX6cNHIA%3D&ctl00%24cphMasterPage%24txbVIN=:VINCODE&ctl00%24cphMasterPage%24btnFindByVIN=%D0%9F%D0%BE%D0%B8%D1%81%D0%BA&ctl00%24cphMasterPage%24rblCountry=el',
         ),
         'Infiniti' => array
         (
            'values' => '__EVENTTARGET=&__EVENTARGUMENT=&__LASTFOCUS=&__VIEWSTATE=%2FwEPDwUJMjEyMjUyNzg0ZBgBBR5fX0NvbnRyb2xzUmVxdWlyZVBvc3RCYWNrS2V5X18WAQUhY3RsMDAkY3BoTWFzdGVyUGFnZSRjaGJTYXZlQ29va2llI3FiW1MXh7RpxF0olzwAX6cNHIA%3D&ctl00%24cphMasterPage%24txbVIN=:VINCODE&ctl00%24cphMasterPage%24btnFindByVIN=%D0%9F%D0%BE%D0%B8%D1%81%D0%BA&ctl00%24cphMasterPage%24rblCountry=el',
         ),
         'Infiniti' => array
         (
            'values' => '__EVENTTARGET=&__EVENTARGUMENT=&__LASTFOCUS=&__VIEWSTATE=%2FwEPDwULLTExMTYxMTU0MDZkGAEFHl9fQ29udHJvbHNSZXF1aXJlUG9zdEJhY2tLZXlfXxYBBSFjdGwwMCRjcGhNYXN0ZXJQYWdlJGNoYlNhdmVDb29raWUIk45hLzmAlwXJbhA4grmPftVPXw%3D%3D&ctl00%24cphMasterPage%24txbVIN=:VINCODE&ctl00%24cphMasterPage%24btnFindByVIN=%D0%9F%D0%BE%D0%B8%D1%81%D0%BA&ctl00%24cphMasterPage%24rblCountry=el',
         ),
         'Suzuki' => array
         (
            'values' => '__EVENTTARGET=&__EVENTARGUMENT=&__LASTFOCUS=&__VIEWSTATE=%2FwEPDwUKLTQ3OTQ1Nzg5MQ9kFgJmD2QWAgIDD2QWAgIDD2QWBAICDw8WCB4JQmFja0NvbG9yCcyZMwAeC0JvcmRlckNvbG9yCqQBHglGb3JlQ29sb3IKpAEeBF8hU0ICHGRkAgUPEGRkFgECBWQYAQUeX19Db250cm9sc1JlcXVpcmVQb3N0QmFja0tleV9fFgEFIWN0bDAwJGNwaE1hc3RlclBhZ2UkY2hiU2F2ZUNvb2tpZVNlJfGjaBMKP%2F837Yeot8QTRG7B&ctl00%24cphMasterPage%24txbVIN=:VINCODE&ctl00%24cphMasterPage%24btnFindByVIN=%D0%9F%D0%BE%D0%B8%D1%81%D0%BA&ctl00%24cphMasterPage%24rblRegions=EU',
         ),
         'Subaru' => array
         (
            'values' => '__EVENTTARGET=&__EVENTARGUMENT=&__LASTFOCUS=&__VIEWSTATE=%2FwEPDwULLTE2MzY4Mzk1NzlkGAEFHl9fQ29udHJvbHNSZXF1aXJlUG9zdEJhY2tLZXlfXxYBBSFjdGwwMCRjcGhNYXN0ZXJQYWdlJGNoYlNhdmVDb29raWX%2BRQhX1Bjv67eApZHjcRmDgE65jg%3D%3D&ctl00%24cphMasterPage%24txbVIN=:VINCODE&ctl00%24cphMasterPage%24btnFindByVIN=%D0%9F%D0%BE%D0%B8%D1%81%D0%BA&ctl00%24cphMasterPage%24rblCountry=e',
         ),
         'Toyota' => array
         (
            'values' => '__EVENTTARGET=&__EVENTARGUMENT=&__LASTFOCUS=&__VIEWSTATE=%2FwEPDwUJODM1NjIxNjg1ZBgBBR5fX0NvbnRyb2xzUmVxdWlyZVBvc3RCYWNrS2V5X18WAgUhY3RsMDAkY3BoTWFzdGVyUGFnZSRjaGJTYXZlQ29va2llBSJjdGwwMCRjcGhNYXN0ZXJQYWdlJGNoYkZpbmRCeUZyYW1lB%2BDoWrCtCGjgqfjxk0GpMwsIc3o%3D&ctl00%24cphMasterPage%24txbVIN=:VINCODE&ctl00%24cphMasterPage%24btnFindByVIN=%D0%9F%D0%BE%D0%B8%D1%81%D0%BA&ctl00%24cphMasterPage%24txbFrame1=&ctl00%24cphMasterPage%24txbFrame2=&ctl00%24cphMasterPage%24rblCountry=E',
         ),
      )
   )
);