# NyarukoDisk
文件上传和共享，还在做。

# 文件列表
如果未单独说明，返回值均为 json 。

### sysinfo.php
#### 功能
显示系统信息，用于告知用户此服务器支持的最大文件大小和超时时间，数据依据 php.ini 中的设定。
#### 提交参数
不需要任何参数。
#### 返回值
- status
  - 0 = 正常
- maxsize
  - 可以提交的最大数据尺寸（包括单位字符）。
- maxfilesize
  - 可以提交的最大文件尺寸（包括单位字符）。
- timeout
  - 等待文件上传完成的超时时间（秒）。

### upload.php
#### 功能
将文件上传到服务器，在数据库中注册文件，返回文件相关资讯。
#### 提交参数
- file/files[] (FILES)
  - 上传的一个或多个文件。
- hash/hash[] (POST)
  - 文件的哈希值（建议 md5，服务端默认算法）。
  - 可选：如果不提交此参数，则由服务器计算 md5。
  - 注意：如果使用此参数提交了非标准 md5 值，则以后都应该使用此参数提交指定的哈希值。
- name/name[] (POST)
  - 文件的名称，应与文件的哈希值的数组排序一致。
  - 可选：如果要「秒传」一个内容重复的文件，需要指定一个新的文件名。文件会被虚拟创建。
  - 注意：一旦该参数有内容，数组中文件名为空白的项目将直接忽略。
#### 返回值
- status
  - 0 = 正常
  - 100 = 文件内容已存在
  - 101 = 文件名存在一些小问题，已自动修改
  - -100 = 目标文件夹不存在
  - -101 = 从临时文件移动到目标位置失败
  - -102 = 文件已存在（意外）
  - 其他信息 = 由 PHP 返回的错误内容
- srcname
  - 原始文件名称
- phyname
  - 物理文件名称
- ext
  - 文件扩展名（依据源文件名称）
- mime
  - 文件类型
- size
  - 文件大小（字节）
- time
  - 文件上传时间
- memory
  - 处理此文件所消耗的内存
- proctime
  - 处理此文件所消耗的时间

### security.php
#### 功能
存储安全相关代码。不从外部调用。

### config.php
#### 功能
包括各项设置，直接修改文件即可。不从外部调用。