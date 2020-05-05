
<template>
  <div class="createNews">
    <el-button @click="submit">提 交</el-button>
    <tinymce-editor :init="init" v-model="result.content"></tinymce-editor><!-- 富文本编辑器 -->

    <div class="formTitle">其它选项</div>
    <el-form class="newsForm" :model="result" :rules="rules"><!-- 表单 -->

      <el-form-item class="title" prop="title"><!-- 文章标题 -->
        <el-input v-model="result.title" placeholder="请输入文章标题"></el-input>
      </el-form-item>

      <el-form-item class="theme_id" prop="theme_id"><!-- 所属专题，可选 -->
        <el-select v-model="result.theme_id" filterable remote reserve-keyword placeholder="请输入关键字查询并选择所属专题，可选" :remote-method="getThemeOption" :loading="themeLoading">
          <el-option v-for="item in themeOption" :key="item.id" :label="item.label" :value="item.id"></el-option>
        </el-select>
      </el-form-item>

      <el-form-item class="master" prop="master"><!-- 文章主标签，单选 -->
        <el-select v-model="result.master" filterable remote reserve-keyword placeholder="请输入关键字查询并添加文章主标签，必填" :remote-method="getMasterOption" :loading="masterLoading">
          <el-option v-for="item in masterOption" :key="item.id" :label="item.label" :value="item.id"></el-option>
        </el-select>
      </el-form-item>

      <el-form-item class="branch" prop="branch"><!-- 文章副标签，多选 -->
        <el-select v-model="result.branch" multiple filterable remote reserve-keyword placeholder="请输入关键字查询并添加文章副标签，可选" :remote-method="getBranchOption" :loading="branchLoading">
          <el-option v-for="item in branchOption" :key="item.id" :label="item.label" :value="item.id"></el-option>
        </el-select>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>

import tinymce from 'tinymce/tinymce';
import Editor from "@tinymce/tinymce-vue";

export default {

  components: { "tinymce-editor": Editor },
  
  data() {
    return {
      init: {
        language_url: "https://adm.sumoli.com/lib/js/zh_CN.js",
        language: "zh_CN",
        height: window.innerHeight * 0.6,
        plugins: "link lists image code table colorpicker textcolor wordcount contextmenu",
        toolbar: "bold italic underline strikethrough | fontsizeselect | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent blockquote | undo redo | link unlink image code | removeformat",
        branding: false,
        images_upload_handler: this.commitImg,
      },

      result: { content: '', title: '', master: null, branch: null, theme_id: null }, // 结果对象
      rules: {
        title: [ { required: true, message: '该项不能为空', trigger: 'blur' } ],
        master: [ { required: true, message: '该项不能为空', trigger: 'blur' } ],
      },

      themeOption: [],
      themeLoading: false,
      
      masterOption: [],
      masterLoading: false,

      branchOption: [],
      branchLoading: false,
    }
  },

  computed: {

  },

  methods: {

    // 处理富文本编辑器实时图片上传
    commitImg(blobInfo, success, failure) {
      let xhr = new XMLHttpRequest();
      let formData = new FormData();
      let url = "http://newsapi.com/api/news/uploadFile";
      let img = blobInfo.blob();
      if(img.size > 1024 * 3000) {
        failure('图片大小不能超过 3M');
        return;
      }
      xhr.withCredentials = false;
      xhr.open("POST", url);
      formData.append("file", blobInfo.blob());
      xhr.onload = function(e) {
        if (xhr.status != 200) {
          failure('HTTP Error: ' + xhr.status);
          return;
        }
        let res = JSON.parse(this.responseText);
        success(res.result);
      };
      xhr.send(formData);
    },

    // 提交
    async submit() {
      async function callback() {
        branch = JSON.stringify(branch);
        let { data: { code, message } } = await this.$gAxios().post('news/add', { content, title, master, branch, theme_id });
        if(code === 200)
          this.$notify.success({ title: message, message: '文章已加入数据库' });
        else
          this.$notify.error({ title: message, message: '添加失败' });
      }
      let { result: { content, title, master, branch, theme_id } } = this;
      if(!content || !title || !master) { // 进行参数校验，title、master、content 不能为空
        this.$notify.warning({ title: '警告', message: '文章内容、文章标题、主标签都不能为空' });
        return;
      }
      let confirmParams = { confirmButtonText: '确定', cancelButtonText: '取消', type: 'info' };
      this.$confirm('确认已编辑完毕并提交吗?', '提示', confirmParams).then(callback.bind(this)).catch(() => null); // 提交前先弹出提示确认
    },

    async getThemeOption(string) {
      if (string !== '') {
        let params = { all: 1, search: string };
        this.themeLoading = true;
        let { data, data: { code, result } } = await this.$gAxios().get('theme/read', { params });
        if(code === 200)
          this.themeOption = result.map(v => ({ id: v.theme_id, label: v.title }));
        else
          this.$notify.error({ title: '错误', message: '获取专题失败' });
        this.themeLoading = false;
      } else
        this.themeOption = [];
    },

    async getMasterOption(string) {
      if (string !== '') {
        let params = { all: 1, search: string };
        this.masterLoading = true;
        let { data: { code, result } } = await this.$gAxios().get('tag/read', { params });
        if(code === 200)
          this.masterOption = result.map(v => ({ id: v.tag_id, label: v.name }));
        else
          this.$notify.error({ title: '错误', message: '获取标签失败' });
        this.masterLoading = false;
      } else
        this.masterOption = [];
    },

    async getBranchOption(string) {
      if (string !== '') {
        let params = { all: 1, search: string };
        this.branchLoading = true;
        let { data: { code, result } } = await this.$gAxios().get('tag/read', { params });
        if(code === 200)
          this.branchOption = result.map(v => ({ id: v.tag_id, label: v.name }));
        else
          this.$notify.error({ title: '错误', message: '获取标签失败' });
        this.branchLoading = false;
      } else
        this.branchOption = [];
    },
  },

  filters: {

  },

  mounted() {

  }
};

</script>

<style lang="scss">
  @import "news.scss";
</style>


