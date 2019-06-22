from django.contrib import admin

# Register your models here.
from django.contrib import admin
from .models import Article

class ArticleAdmin(admin.ModelAdmin):
    list_display = ('title','content','pub_date','update_time',)

admin.site.register(Article,ArticleAdmin)